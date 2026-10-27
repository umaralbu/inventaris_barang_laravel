<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'user', 'returnTransaction.user'])
            ->where('type', '!=', 'return')
            ->orderByDesc('transaction_date');

        if ($request->filled('type')) {
            if ($request->type === 'borrow') {
                $query->where('type', 'borrow')->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan']);
            } elseif ($request->type === 'return') {
                $query->where('type', 'borrow')->where('status', 'Dikembalikan');
            } elseif ($request->type === 'transfer') {
                $query->where('type', 'transfer')->where('status', 'Dipindahkan');
            }
        }

        if ($request->filled('user_id')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->where('user_id', $request->user_id));
            } else {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->filled('date')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereDate('transaction_date', $request->date));
            } else {
                $query->whereDate('transaction_date', $request->date);
            }
        }

        if ($request->filled('time')) {
            $timePrefix = substr($request->time, 0, 5);
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereTime('transaction_date', 'like', "{$timePrefix}%"));
            } else {
                $query->whereTime('transaction_date', 'like', "{$timePrefix}%");
            }
        }

        $transactions = $query->paginate(15)->appends($request->query());

        return view('transactions.index', compact('transactions'));
    }

    public function borrowForm()
    {
        return view('transactions.borrow', [
            'items' => Item::with('location')->where('is_active', true)->where('status', 'Tersedia')->where('quantity', '>', 0)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'staff' => Staff::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function borrowStore(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'staff_id' => ['required', Rule::exists('staff', 'id')->where('is_active', true)],
            'to_location' => ['required', Rule::exists('locations', 'name')->where('is_active', true)],
            'purpose' => ['nullable', 'string'],
            'transaction_date' => ['required', 'date'],
            'transaction_time' => ['nullable', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'transaction_time.regex' => 'Format waktu transaksi harus 24 jam (HH:mm).',
        ]);

        $fullDateTime = $this->parseTransactionDateTime($validated['transaction_date'], $validated['transaction_time'] ?? null);

        DB::transaction(function () use ($validated, $fullDateTime) {
            $item = Item::whereKey($validated['item_id'])->lockForUpdate()->firstOrFail();
            $staff = Staff::where('is_active', true)->findOrFail($validated['staff_id']);

            if (!$item->is_active || $item->status !== 'Tersedia' || $item->quantity < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang tidak tersedia atau jumlah stok tidak mencukupi.',
                ]);
            }

            Transaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'borrow',
                'status' => 'Menunggu Persetujuan',
                'from_location' => $item->location?->name ?? 'Tidak diketahui',
                'to_location' => $validated['to_location'],
                'from_person' => 'Admin',
                'to_person' => $staff->name,
                'quantity' => $validated['quantity'],
                'transaction_date' => $fullDateTime,
                'condition' => $item->condition,
                'notes' => !empty($validated['purpose']) ? $validated['purpose'] : ($validated['notes'] ?? null),
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Permintaan pinjaman berhasil dikirim dan menunggu persetujuan admin.');
    }

    public function returnForm(?Item $item = null)
    {
        return view('transactions.return', [
            'item' => $item,
            'activeBorrowTransactions' => Transaction::with('item')
                ->where('type', 'borrow')
                ->where('status', 'Dipinjam')
                ->whereHas('item')
                ->latest('transaction_date')
                ->get(),
            'pendingBorrowCount' => Transaction::where('type', 'borrow')
                ->where('status', 'Menunggu Persetujuan')
                ->count(),
            'staff' => Staff::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function returnStore(Request $request)
    {
        $validated = $request->validate([
            'borrow_transaction_id' => ['required', 'exists:transactions,id'],
            'from_person' => ['required', Rule::exists('staff', 'name')->where('is_active', true)],
            'to_location' => ['required', Rule::exists('locations', 'name')->where('is_active', true)],
            'transaction_date' => ['required', 'date'],
            'transaction_time' => ['nullable', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/'],
            'condition' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ], [
            'transaction_date.required' => 'Tanggal pengembalian wajib diisi.',
            'transaction_date.date' => 'Format tanggal pengembalian tidak valid.',
            'transaction_time.regex' => 'Format waktu transaksi harus 24 jam (HH:mm).',
        ]);

        $fullDateTime = $this->parseTransactionDateTime($validated['transaction_date'], $validated['transaction_time'] ?? null);

        DB::transaction(function () use ($validated, $fullDateTime) {
            $borrow = Transaction::whereKey($validated['borrow_transaction_id'])
                ->where('type', 'borrow')
                ->where('status', 'Dipinjam')
                ->lockForUpdate()
                ->first();

            if (!$borrow) {
                throw ValidationException::withMessages([
                    'borrow_transaction_id' => 'Transaksi pinjaman tidak aktif atau sudah dikembalikan.',
                ]);
            }

            $item = Item::whereKey($borrow->item_id)->lockForUpdate()->firstOrFail();
            $borrowedQuantity = $borrow->quantity;

            $borrow->update([
                'status' => 'Dikembalikan',
                'notes' => trim(($borrow->notes ? $borrow->notes . ' | ' : '') . 'Pengembalian oleh ' . $validated['from_person'] . ', ke ' . $validated['to_location'] . ', kondisi ' . $validated['condition'] . (empty($validated['notes']) ? '' : ', catatan: ' . $validated['notes'])),
            ]);

            Transaction::create([
                'item_id' => $item->id,
                'borrow_transaction_id' => $borrow->id,
                'user_id' => Auth::id(),
                'type' => 'return',
                'status' => 'Dikembalikan',
                'from_location' => $validated['to_location'],
                'to_location' => $validated['to_location'],
                'from_person' => $validated['from_person'],
                'to_person' => 'Admin',
                'quantity' => $borrowedQuantity,
                'transaction_date' => $fullDateTime,
                'condition' => $validated['condition'],
                'notes' => $validated['notes'],
            ]);

            $hasOtherActiveBorrow = Transaction::where('item_id', $item->id)
                ->where('type', 'borrow')
                ->where('status', 'Dipinjam')
                ->where('id', '!=', $borrow->id)
                ->exists();

            $item->update([
                'status' => $hasOtherActiveBorrow
                    ? 'Dipinjam'
                    : ($validated['condition'] === 'Baik' ? 'Tersedia' : 'Rusak'),
                'quantity' => $item->quantity + $borrowedQuantity,
                'condition' => $validated['condition'],
                'location_id' => Location::where('name', $validated['to_location'])
                    ->where('is_active', true)
                    ->firstOrFail()
                    ->id,
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Pengembalian barang berhasil dicatat.');
    }

    public function transferForm()
    {
        return view('transactions.transfer', [
            'items' => Item::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'staff' => Staff::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function transferStore(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', Rule::exists('items', 'id')->where('is_active', true)],
            'from_location' => ['required', Rule::exists('locations', 'name')->where('is_active', true)],
            'to_location' => ['required', Rule::exists('locations', 'name')->where('is_active', true)],
            'transferred_by' => ['required', Rule::exists('staff', 'name')->where('is_active', true)],
            'transaction_date' => ['required', 'date'],
            'transaction_time' => ['nullable', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ], [
            'transaction_date.required' => 'Tanggal perpindahan wajib diisi.',
            'transaction_date.date' => 'Format tanggal perpindahan tidak valid.',
            'transaction_time.regex' => 'Format waktu transaksi harus 24 jam (HH:mm).',
        ]);

        $fullDateTime = $this->parseTransactionDateTime($validated['transaction_date'], $validated['transaction_time'] ?? null);

        $item = Item::findOrFail($validated['item_id']);

        Transaction::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'transfer',
            'from_location' => $validated['from_location'],
            'to_location' => $validated['to_location'],
            'from_person' => $validated['transferred_by'],
            'to_person' => $validated['transferred_by'],
            'quantity' => $item->quantity,
            'transaction_date' => $fullDateTime,
            'condition' => $item->condition,
            'notes' => ($validated['reason'] ?? '') . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
        ]);

        $item->update([
            'status' => 'Dipindahkan',
            'location_id' => Location::firstOrCreate(['name' => $validated['to_location']])->id,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Perpindahan barang berhasil dicatat.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['item', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    public function approve(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $borrow = Transaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();

            if ($borrow->type !== 'borrow' || $borrow->status !== 'Menunggu Persetujuan') {
                throw ValidationException::withMessages(['status' => 'Permintaan pinjaman tidak valid untuk disetujui.']);
            }

            $item = Item::whereKey($borrow->item_id)->lockForUpdate()->first();

            if (!$item || !$item->is_active || $item->status !== 'Tersedia' || $item->quantity < $borrow->quantity) {
                throw ValidationException::withMessages(['status' => 'Barang tidak tersedia atau stok tidak cukup untuk persetujuan ini.']);
            }

            $destination = Location::where('name', $borrow->to_location)
                ->where('is_active', true)
                ->first();

            if (!$destination) {
                throw ValidationException::withMessages(['status' => 'Lokasi tujuan sudah tidak aktif. Ajukan pinjaman dengan lokasi aktif.']);
            }

            $borrow->update(['status' => 'Dipinjam']);
            $remainingQuantity = $item->quantity - $borrow->quantity;
            $item->update([
                'status' => $remainingQuantity > 0 ? 'Tersedia' : 'Dipinjam',
                'quantity' => $remainingQuantity,
                'location_id' => $destination->id,
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Pinjaman barang berhasil disetujui.');
    }

    public function reject(Transaction $transaction)
    {
        if ($transaction->type !== 'borrow' || $transaction->status !== 'Menunggu Persetujuan') {
            return back()->withErrors(['status' => 'Permintaan pinjaman tidak valid untuk ditolak.']);
        }

        $transaction->update(['status' => 'Ditolak']);

        return redirect()->route('transactions.index')->with('success', 'Permintaan pinjaman ditolak.');
    }

    public function destroy(Transaction $transaction)
    {
        $borrow = $transaction->type === 'borrow'
            ? $transaction
            : $transaction->borrowTransaction;
        $return = $transaction->type === 'return'
            ? $transaction
            : $transaction->returnTransaction;

        if ($borrow && $return && $borrow->status === 'Dikembalikan' && $return->status === 'Dikembalikan') {
            abort_unless(Auth::user()->role === 'admin' || $return->user_id === Auth::id(), 403);

            DB::transaction(function () use ($transaction) {
                $selected = Transaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();
                $borrowId = $selected->type === 'borrow' ? $selected->id : $selected->borrow_transaction_id;
                $borrow = Transaction::whereKey($borrowId)
                    ->where('type', 'borrow')
                    ->where('status', 'Dikembalikan')
                    ->lockForUpdate()
                    ->first();

                $return = Transaction::where('borrow_transaction_id', $borrowId)
                    ->where('type', 'return')
                    ->where('status', 'Dikembalikan')
                    ->lockForUpdate()
                    ->first();

                if (!$borrow || !$return) {
                    throw ValidationException::withMessages([
                        'transaction' => 'Transaksi pinjam pasangan tidak ditemukan; tracking tidak dihapus.',
                    ]);
                }

                $return->delete();
                $borrow->delete();
            });

            return redirect()->route('transactions.index')->with('success', 'Tracking pinjaman dan pengembalian berhasil dihapus. Stok barang tidak berubah.');
        }

        if (Auth::user()->role === 'admin' && $transaction->type === 'borrow' && in_array($transaction->status, ['Menunggu Persetujuan', 'Ditolak'], true)) {
            $transaction->delete();

            return redirect()->route('transactions.index')->with('success', 'Pengajuan pinjaman berhasil dihapus.');
        }

        return back()->withErrors([
            'transaction' => 'Hanya pengajuan yang belum aktif atau tracking pengembalian yang sudah selesai yang dapat dihapus.',
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = Transaction::with(['item', 'user', 'returnTransaction.user'])
            ->where('type', '!=', 'return')
            ->orderByDesc('transaction_date');

        if ($request->filled('type')) {
            if ($request->type === 'borrow') {
                $query->where('type', 'borrow')->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan']);
            } elseif ($request->type === 'return') {
                $query->where('type', 'borrow')->where('status', 'Dikembalikan');
            } elseif ($request->type === 'transfer') {
                $query->where('type', 'transfer')->where('status', 'Dipindahkan');
            }
        }

        if ($request->filled('user_id')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->where('user_id', $request->user_id));
            } else {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->filled('date')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereDate('transaction_date', $request->date));
            } else {
                $query->whereDate('transaction_date', $request->date);
            }
        }

        if ($request->filled('time')) {
            $timePrefix = substr($request->time, 0, 5);
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereTime('transaction_date', 'like', "{$timePrefix}%"));
            } else {
                $query->whereTime('transaction_date', 'like', "{$timePrefix}%");
            }
        }

        $transactions = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transactions-export.csv"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'ID Transaksi',
                'Kode Barang',
                'Nama Barang',
                'Jenis Transaksi',
                'Peminjam / Penanggung Jawab',
                'Jumlah',
                'Dari Lokasi',
                'Ke Lokasi',
                'Status',
                'Tanggal & Waktu Transaksi',
                'Kondisi',
                'Keterangan / Keperluan',
                'Dicatat Oleh',
            ]);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->item?->code ?? '-',
                    $t->item?->name ?? '-',
                    ucfirst($t->type),
                    $t->to_person ?? $t->from_person ?? '-',
                    $t->quantity,
                    $t->from_location ?? '-',
                    $t->to_location ?? '-',
                    $t->status ?? '-',
                    $t->formatted_transaction_date,
                    $t->condition ?? '-',
                    $t->notes ?? '-',
                    $t->user?->name ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'transactions-export.csv', $headers);
    }

    protected function parseTransactionDateTime(string $date, ?string $time): string
    {
        $time = trim($time ?? '');
        if ($time === '') {
            $time = now('Asia/Jakarta')->format('H:i:s');
        } elseif (preg_match('/^\d{2}:\d{2}$/', $time)) {
            $current = now('Asia/Jakarta');
            if ($time === $current->format('H:i')) {
                $time .= ':' . $current->format('s');
            } else {
                $time .= ':00';
            }
        }

        return \Carbon\Carbon::parse("{$date} {$time}", 'Asia/Jakarta')->format('Y-m-d H:i:s');
    }
}
