<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $items = DB::table('items')->orderBy('id')->get();
        $transactions = DB::table('transactions')->orderBy('id')->get();
        $transactionsByItem = $transactions->groupBy('item_id');
        $activeBorrowsByItem = $transactions
            ->where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->groupBy('item_id');
        $unitIdsByItem = [];
        $itemRowsById = [];

        DB::transaction(function () use ($items, $transactions, $transactionsByItem, $activeBorrowsByItem, &$unitIdsByItem, &$itemRowsById) {
            foreach ($items as $item) {
                $availableQuantity = max(0, (int) $item->quantity);
                $activeBorrowQuantity = (int) ($activeBorrowsByItem->get($item->id, collect())->sum('quantity'));
                $transactionQuantity = (int) ($transactionsByItem->get($item->id, collect())
                    ->reject(fn ($transaction) => $transaction->type === 'borrow' && $transaction->status === 'Menunggu Persetujuan')
                    ->max('quantity') ?? 1);
                $unitCount = max($availableQuantity + $activeBorrowQuantity, $transactionQuantity, 1);
                $itemRowsById[$item->id] = $item;
                $unitIdsByItem[$item->id] = [(int) $item->id];

                for ($unitNumber = 2; $unitNumber <= $unitCount; $unitNumber++) {
                    $attributes = (array) $item;
                    unset($attributes['id']);
                    $attributes['code'] = $this->uniqueUnitCode($item->code, $item->id, $unitNumber);
                    $attributes['quantity'] = 1;
                    $attributes['created_at'] = $item->created_at;
                    $attributes['updated_at'] = now();

                    $unitIdsByItem[$item->id][] = (int) DB::table('items')->insertGetId($attributes);
                }

                DB::table('items')->where('id', $item->id)->update([
                    'quantity' => 1,
                    'updated_at' => now(),
                ]);
            }

            $activeBorrowUnitIds = $this->splitTransactions($transactions, $unitIdsByItem);

            foreach ($unitIdsByItem as $itemId => $unitIds) {
                $item = $itemRowsById[$itemId];
                $availableStatus = $item->status;

                if ($availableStatus === 'Dipindahkan'
                    || ($availableStatus === 'Dipinjam' && (int) $item->quantity > 0)) {
                    $availableStatus = $item->condition === 'Baik' ? 'Tersedia' : $item->condition;
                }

                DB::table('items')->whereIn('id', $unitIds)->update([
                    'status' => $availableStatus,
                    'quantity' => 1,
                ]);
            }

            if ($activeBorrowUnitIds) {
                DB::table('items')->whereIn('id', array_unique($activeBorrowUnitIds))->update([
                    'status' => 'Dipinjam',
                ]);
            }
        });

        $this->restrictDeletingItemsWithHistory();
        $this->addSingleUnitConstraints();
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS items_quantity_one_insert');
            DB::statement('DROP TRIGGER IF EXISTS items_quantity_one_update');
            DB::statement('DROP TRIGGER IF EXISTS transactions_quantity_one_insert');
            DB::statement('DROP TRIGGER IF EXISTS transactions_quantity_one_update');
            DB::statement('DROP TRIGGER IF EXISTS items_prevent_delete_with_transactions');

            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            if ($this->hasConstraint('items', 'items_quantity_one_unit')) {
                DB::statement('ALTER TABLE items DROP CONSTRAINT items_quantity_one_unit');
            }
            if ($this->hasConstraint('transactions', 'transactions_quantity_one_unit')) {
                DB::statement('ALTER TABLE transactions DROP CONSTRAINT transactions_quantity_one_unit');
            }
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
                $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            });
        }
    }

    private function restrictDeletingItemsWithHistory(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
                $table->foreign('item_id')->references('id')->on('items')->restrictOnDelete();
            });
        } elseif ($driver === 'sqlite') {
            DB::statement("CREATE TRIGGER items_prevent_delete_with_transactions BEFORE DELETE ON items WHEN EXISTS (SELECT 1 FROM transactions WHERE item_id = OLD.id) BEGIN SELECT RAISE(ABORT, 'Items with transaction history cannot be deleted'); END");
        }
    }

    private function splitTransactions($transactions, array $unitIdsByItem): array
    {
        $borrowUnitIds = [];
        $activeUnitOffsets = [];

        foreach ($transactions->where('type', 'borrow')->where('status', 'Dipinjam') as $transaction) {
            $unitIds = $unitIdsByItem[$transaction->item_id] ?? [];
            $quantity = max(1, (int) $transaction->quantity);
            $offset = $activeUnitOffsets[$transaction->item_id] ?? 0;
            $borrowUnitIds[$transaction->id] = array_slice($unitIds, $offset, $quantity);
            $activeUnitOffsets[$transaction->item_id] = $offset + $quantity;

            if (count($borrowUnitIds[$transaction->id]) !== $quantity) {
                throw new RuntimeException("Transaksi {$transaction->id} tidak dapat dipetakan ke unit barang yang cukup.");
            }
        }

        foreach ($transactions->where('type', 'borrow')->reject(fn ($transaction) => isset($borrowUnitIds[$transaction->id])) as $transaction) {
            $unitIds = $unitIdsByItem[$transaction->item_id] ?? [];
            $quantity = max(1, (int) $transaction->quantity);

            if ($transaction->status === 'Menunggu Persetujuan') {
                $activeUnits = [];
                foreach ($transactions->where('type', 'borrow')
                    ->where('status', 'Dipinjam')
                    ->where('item_id', $transaction->item_id) as $activeBorrow) {
                    $activeUnits = array_merge($activeUnits, $borrowUnitIds[$activeBorrow->id]);
                }
                $unitIds = array_values(array_diff($unitIds, $activeUnits));
            }

            $borrowUnitIds[$transaction->id] = array_slice($unitIds, 0, $quantity);

            if (count($borrowUnitIds[$transaction->id]) !== $quantity) {
                throw new RuntimeException("Transaksi {$transaction->id} tidak dapat dipetakan ke unit barang yang cukup.");
            }
        }

        $activeBorrowUnitIds = [];
        foreach ($transactions->where('type', 'borrow')->where('status', 'Dipinjam') as $transaction) {
            $activeBorrowUnitIds = array_merge($activeBorrowUnitIds, $borrowUnitIds[$transaction->id]);
        }

        $newTransactionIdsByUnit = [];
        foreach ($transactions->where('type', 'borrow') as $transaction) {
            $newTransactionIdsByUnit[$transaction->id] = $this->writeUnitTransactions(
                $transaction,
                $borrowUnitIds[$transaction->id],
                []
            );
        }

        foreach ($transactions->where('type', '!=', 'borrow') as $transaction) {
            $quantity = max(1, (int) $transaction->quantity);
            $unitIds = $unitIdsByItem[$transaction->item_id] ?? [];
            $parentUnitIds = $newTransactionIdsByUnit[$transaction->borrow_transaction_id] ?? [];

            if ($transaction->type === 'return' && $parentUnitIds) {
                $unitIds = array_keys($parentUnitIds);
            }

            $unitIds = array_slice($unitIds, 0, $quantity);

            if (count($unitIds) !== $quantity) {
                throw new RuntimeException("Transaksi {$transaction->id} tidak dapat dipetakan ke unit barang yang cukup.");
            }

            $newTransactionIdsByUnit[$transaction->id] = $this->writeUnitTransactions(
                $transaction,
                $unitIds,
                $parentUnitIds
            );
        }

        return $activeBorrowUnitIds;
    }

    private function writeUnitTransactions(object $transaction, array $unitIds, array $parentUnitIds): array
    {
        $unitTransactionIds = [];

        foreach ($unitIds as $index => $unitId) {
            $attributes = (array) $transaction;
            unset($attributes['id']);
            $attributes['item_id'] = $unitId;
            $attributes['quantity'] = 1;

            if ($transaction->type === 'return' && isset($parentUnitIds[$unitId])) {
                $attributes['borrow_transaction_id'] = $parentUnitIds[$unitId];
            }

            if ($index === 0) {
                DB::table('transactions')->where('id', $transaction->id)->update([
                    'item_id' => $unitId,
                    'quantity' => 1,
                    'borrow_transaction_id' => $attributes['borrow_transaction_id'] ?? $transaction->borrow_transaction_id,
                ]);
                $unitTransactionIds[$unitId] = (int) $transaction->id;
            } else {
                $unitTransactionIds[$unitId] = (int) DB::table('transactions')->insertGetId($attributes);
            }
        }

        return $unitTransactionIds;
    }

    private function uniqueUnitCode(string $baseCode, int $itemId, int $unitNumber): string
    {
        $suffix = '-' . str_pad((string) $unitNumber, 2, '0', STR_PAD_LEFT);
        $candidate = substr($baseCode, 0, 255 - strlen($suffix)) . $suffix;

        if (!DB::table('items')->where('code', $candidate)->exists()) {
            return $candidate;
        }

        $suffix = '-MIG-' . $itemId . '-' . $unitNumber;
        $candidate = substr($baseCode, 0, 255 - strlen($suffix)) . $suffix;

        while (DB::table('items')->where('code', $candidate)->exists()) {
            $suffix .= '-1';
            $candidate = substr($baseCode, 0, 255 - strlen($suffix)) . $suffix;
        }

        return $candidate;
    }

    private function addSingleUnitConstraints(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            if (!$this->hasConstraint('items', 'items_quantity_one_unit')) {
                DB::statement('ALTER TABLE items ADD CONSTRAINT items_quantity_one_unit CHECK (quantity = 1)');
            }
            if (!$this->hasConstraint('transactions', 'transactions_quantity_one_unit')) {
                DB::statement('ALTER TABLE transactions ADD CONSTRAINT transactions_quantity_one_unit CHECK (quantity = 1)');
            }

            return;
        }

        if ($driver === 'sqlite') {
            DB::statement("CREATE TRIGGER IF NOT EXISTS items_quantity_one_insert BEFORE INSERT ON items WHEN NEW.quantity != 1 BEGIN SELECT RAISE(ABORT, 'Each item row must represent one unit'); END");
            DB::statement("CREATE TRIGGER IF NOT EXISTS items_quantity_one_update BEFORE UPDATE OF quantity ON items WHEN NEW.quantity != 1 BEGIN SELECT RAISE(ABORT, 'Each item row must represent one unit'); END");
            DB::statement("CREATE TRIGGER IF NOT EXISTS transactions_quantity_one_insert BEFORE INSERT ON transactions WHEN NEW.quantity != 1 BEGIN SELECT RAISE(ABORT, 'Each transaction row must represent one unit'); END");
            DB::statement("CREATE TRIGGER IF NOT EXISTS transactions_quantity_one_update BEFORE UPDATE OF quantity ON transactions WHEN NEW.quantity != 1 BEGIN SELECT RAISE(ABORT, 'Each transaction row must represent one unit'); END");
        }
    }

    private function hasConstraint(string $table, string $constraint): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $constraint)
            ->exists();
    }
};
