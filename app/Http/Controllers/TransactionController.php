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
        $query = Transaction::with(['item', 'user', 'returnTransaction.user', 'lossReports.user'])
            ->whereIn('type', ['borrow', 'transfer'])
            ->orderByDesc('transaction_date');

        if ($request->filled('type')) {
            if ($request->type === 'borrow') {
                $query->where('type', 'borrow')->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan']);
            } elseif ($request->type === 'return') {
                $query->where('type', 'borrow')->where('status', 'Dikembalikan');
            } elseif ($request->type === 'lost') {
                $query->where('type', 'borrow')->where('status', 'Hilang');
            } elseif ($request->type === 'found') {
                $query->where('type', 'borrow')->where('status', 'Ditemukan');
            } elseif ($request->type === 'loss') {
                $query->where('type', 'borrow')->whereHas('lossReports', fn ($reportQuery) => $reportQuery->where('status', 'Menunggu Konfirmasi'));
            } elseif ($request->type === 'transfer') {
                $query->where('type', 'transfer')->where('status', 'Dipindahkan');
            }
        }

        if ($request->filled('user_id')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->where('user_id', $request->user_id));
            } elseif ($request->type === 'loss') {
                $query->whereHas('lossReports', fn ($reportQuery) => $reportQuery->where('user_id', $request->user_id));
            } else {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->filled('date')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereDate('transaction_date', $request->date));
            } elseif ($request->type === 'loss') {
                $query->whereHas('lossReports', fn ($reportQuery) => $reportQuery->whereDate('transaction_date', $request->date));
            } else {
                $query->whereDate('transaction_date', $request->date);
            }
        }

        if ($request->filled('time')) {
            $timePrefix = substr($request->time, 0, 5);
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereTime('transaction_date', 'like', "{$timePrefix}%"));
            } elseif ($request->type === 'loss') {
                $query->whereHas('lossReports', fn ($reportQuery) => $reportQuery->whereTime('transaction_date', 'like', "{$timePrefix}%"));
            } else {
                $query->whereTime('transaction_date', 'like', "{$timePrefix}%");
            }
        }

        $transactions = $query->paginate(15)->appends($request->query());
        $overdueTransactions = Transaction::with(['item', 'user'])
            ->overdue()
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->get();

        return view('transactions.index', compact('transactions', 'overdueTransactions'));
    }

    public function borrowForm()
    {
        return view('transactions.borrow', [
            'items' => Item::with('location')
                ->where('is_active', true)
                ->where('status', 'Tersedia')
                ->where('condition', '!=', 'Rusak')
                ->where('quantity', '>', 0)
                ->whereDoesntHave('transactions', fn ($query) => $query
                    ->where('type', 'borrow')
                    ->where('status', 'Menunggu Persetujuan'))
                ->orderBy('name')
                ->get(),
            'locations' => Location::where('is_active', true)->where('name', '!=', 'Gudang')->orderBy('name')->get(),
            'staff' => Staff::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function borrowStore(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1', 'max:100'],
            'item_ids.*' => ['required', 'integer', 'distinct', 'exists:items,id'],
            'staff_id' => ['required', Rule::exists('staff', 'id')->where('is_active', true)],
            'item_locations' => ['required', 'array'],
            'item_locations.*' => ['required', 'string', Rule::exists('locations', 'name')->where(fn ($query) => $query->where('is_active', true)->where('name', '!=', 'Gudang'))],
            'item_purposes' => ['nullable', 'array'],
            'item_purposes.*' => ['nullable', 'string'],
            'item_due_datetimes' => ['required', 'array'],
            'item_due_datetimes.*' => ['required', 'date_format:Y-m-d\TH:i'],
            'purpose' => ['nullable', 'string'],
            'transaction_date' => ['required', 'date'],
            'transaction_time' => ['nullable', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/'],
            'notes' => ['nullable', 'string'],
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'transaction_time.regex' => 'Format waktu transaksi harus 24 jam (HH:mm).',
        ]);

        $fullDateTime = $this->parseTransactionDateTime($validated['transaction_date'], $validated['transaction_time'] ?? null);
        $itemIds = $validated['item_ids'];
        $dueDateTimes = [];
        foreach ($itemIds as $itemId) {
            if (!array_key_exists($itemId, $validated['item_locations'])) {
                throw ValidationException::withMessages([
                    'item_locations' => 'Pilih lokasi tujuan untuk setiap barang yang diambil.',
                ]);
            }

            if (!array_key_exists($itemId, $validated['item_due_datetimes'])) {
                throw ValidationException::withMessages([
                    'item_due_datetimes' => 'Isi tanggal jatuh tempo untuk setiap barang yang diambil.',
                ]);
            }

            $dueDateTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['item_due_datetimes'][$itemId], 'Asia/Jakarta');
            if ($dueDateTime->lessThanOrEqualTo(\Carbon\Carbon::parse($fullDateTime, 'Asia/Jakarta'))) {
                throw ValidationException::withMessages([
                    "item_due_datetimes.{$itemId}" => 'Tanggal jatuh tempo harus setelah tanggal dan waktu transaksi.',
                ]);
            }

            $dueDateTimes[$itemId] = $dueDateTime;
        }

        DB::transaction(function () use ($validated, $fullDateTime, $dueDateTimes, $itemIds) {
            $items = Item::whereIn('id', $itemIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $pendingItemIds = Transaction::whereIn('item_id', $itemIds)
                ->where('type', 'borrow')
                ->where('status', 'Menunggu Persetujuan')
                ->pluck('item_id')
                ->map(fn ($id) => (int) $id)
                ->all();
            $staff = Staff::where('is_active', true)->findOrFail($validated['staff_id']);

            if ($items->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'item_ids' => 'Salah satu barang yang dipilih sudah tidak tersedia. Silakan pilih ulang.',
                ]);
            }

            foreach ($itemIds as $itemId) {
                $item = $items->get($itemId);
                if (!$item->is_active || $item->status !== 'Tersedia' || $item->condition === 'Rusak' || $item->quantity < 1 || in_array((int) $item->id, $pendingItemIds, true)) {
                    throw ValidationException::withMessages([
                        'item_ids' => "Barang {$item->name} ({$item->code}) sudah tidak tersedia. Silakan pilih ulang.",
                    ]);
                }
            }

            foreach ($itemIds as $itemId) {
                $item = $items->get($itemId);
                Transaction::create([
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => 'borrow',
                    'status' => 'Menunggu Persetujuan',
                    'from_location' => $item->location?->name ?? 'Tidak diketahui',
                    'to_location' => $validated['item_locations'][$itemId],
                    'from_person' => 'Admin',
                    'to_person' => $staff->name,
                    'quantity' => 1,
                    'transaction_date' => $fullDateTime,
                    'due_date' => $dueDateTimes[$itemId]->toDateString(),
                    'due_time' => $dueDateTimes[$itemId]->format('H:i:s'),
                    'condition' => $item->condition,
                    'notes' => $validated['item_purposes'][$itemId] ?? ($validated['purpose'] ?? ($validated['notes'] ?? null)),
                ]);
            }
        });

        $itemCount = count($itemIds);

        return redirect()->route('transactions.index')->with('success', "Permintaan pinjaman untuk {$itemCount} unit barang berhasil dikirim dan menunggu persetujuan admin.");
    }

    public function returnForm(?Item $item = null)
    {
        return view('transactions.return', [
            'item' => $item,
            'activeBorrowTransactions' => Transaction::with('item')
                ->where('type', 'borrow')
                ->where('status', 'Dipinjam')
                ->whereHas('item')
                ->whereDoesntHave('lossReports', fn ($query) => $query->where('status', 'Menunggu Konfirmasi'))
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
            'transaction_time.regex' => 'Format waktu transaksi harus 24 jam (HH:mm:ss).',
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

            $hasPendingLossReport = Transaction::where('borrow_transaction_id', $borrow->id)
                ->where('type', 'loss_report')
                ->where('status', 'Menunggu Konfirmasi')
                ->exists();

            if ($hasPendingLossReport) {
                throw ValidationException::withMessages([
                    'borrow_transaction_id' => 'Pengembalian tidak dapat dicatat selama laporan kehilangan menunggu konfirmasi admin.',
                ]);
            }

            $item = Item::whereKey($borrow->item_id)->lockForUpdate()->firstOrFail();
            $borrowedQuantity = $borrow->quantity;
            if ($validated['condition'] === 'Rusak') {
                $returnLocation = Location::ensureActive('Gudang');
            } elseif ($validated['to_location'] === 'Gudang') {
                $returnLocation = Location::ensureActive('Ruang Penyimpanan');
            } else {
                $returnLocation = Location::where('name', $validated['to_location'])
                    ->where('is_active', true)
                    ->firstOrFail();
            }

            $borrow->update([
                'status' => 'Dikembalikan',
                'notes' => trim(($borrow->notes ? $borrow->notes . ' | ' : '') . 'Pengembalian oleh ' . $validated['from_person'] . ', ke ' . $returnLocation->name . ', kondisi ' . $validated['condition'] . (empty($validated['notes']) ? '' : ', catatan: ' . $validated['notes'])),
            ]);

            Transaction::create([
                'item_id' => $item->id,
                'borrow_transaction_id' => $borrow->id,
                'user_id' => Auth::id(),
                'type' => 'return',
                'status' => 'Dikembalikan',
                'from_location' => $returnLocation->name,
                'to_location' => $returnLocation->name,
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
                    : 'Tersedia',
                'condition' => $validated['condition'],
                'location_id' => $returnLocation->id,
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Pengembalian barang berhasil dicatat.');
    }

    public function reportLost(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ], [
            'notes.required' => 'Keterangan kehilangan wajib diisi.',
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $borrow = Transaction::whereKey($transaction->id)
                ->where('type', 'borrow')
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(Auth::user()->role === 'user' && $borrow->user_id === Auth::id(), 403);

            if ($borrow->status !== 'Dipinjam') {
                throw ValidationException::withMessages([
                    'transaction' => 'Hanya pinjaman aktif yang dapat dilaporkan hilang.',
                ]);
            }

            $hasPendingReport = Transaction::where('borrow_transaction_id', $borrow->id)
                ->where('type', 'loss_report')
                ->where('status', 'Menunggu Konfirmasi')
                ->exists();

            if ($hasPendingReport) {
                throw ValidationException::withMessages([
                    'transaction' => 'Laporan kehilangan untuk pinjaman ini sedang menunggu konfirmasi admin.',
                ]);
            }

            $item = Item::whereKey($borrow->item_id)->lockForUpdate()->firstOrFail();

            Transaction::create([
                'item_id' => $item->id,
                'borrow_transaction_id' => $borrow->id,
                'user_id' => Auth::id(),
                'type' => 'loss_report',
                'status' => 'Menunggu Konfirmasi',
                'from_location' => $borrow->to_location,
                'from_person' => Auth::user()->name,
                'to_person' => $borrow->to_person,
                'quantity' => $borrow->quantity,
                'transaction_date' => now('Asia/Jakarta'),
                'condition' => $borrow->condition,
                'notes' => trim($validated['notes']),
            ]);
        });

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Laporan kehilangan berhasil dikirim dan menunggu konfirmasi admin. Pinjaman tetap aktif sampai dikonfirmasi.');
    }

    public function confirmLost(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $report = Transaction::whereKey($transaction->id)
                ->where('type', 'loss_report')
                ->lockForUpdate()
                ->firstOrFail();

            if ($report->status !== 'Menunggu Konfirmasi') {
                throw ValidationException::withMessages([
                    'status' => 'Laporan kehilangan ini sudah ditinjau atau tidak valid.',
                ]);
            }

            $borrow = Transaction::whereKey($report->borrow_transaction_id)
                ->where('type', 'borrow')
                ->lockForUpdate()
                ->first();

            if (!$borrow || $borrow->status !== 'Dipinjam') {
                throw ValidationException::withMessages([
                    'status' => 'Pinjaman terkait sudah tidak aktif; laporan tidak dapat dikonfirmasi.',
                ]);
            }

            $item = Item::whereKey($report->item_id)->lockForUpdate()->firstOrFail();
            $reviewNote = 'Laporan kehilangan dikonfirmasi oleh ' . Auth::user()->name . ' pada ' . now('Asia/Jakarta')->format('d-m-Y H:i:s') . '.';

            $report->update([
                'status' => 'Dikonfirmasi',
                'notes' => trim($report->notes . ' | ' . $reviewNote),
            ]);
            $borrow->update(['status' => 'Hilang']);
            $item->update(['status' => 'Hilang']);
        });

        return redirect()->route('transactions.index')->with('success', 'Kehilangan dikonfirmasi. Pinjaman ditutup sebagai hilang dan kondisi fisik barang tetap seperti sebelumnya.');
    }

    public function rejectLost(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $report = Transaction::whereKey($transaction->id)
                ->where('type', 'loss_report')
                ->lockForUpdate()
                ->firstOrFail();

            if ($report->status !== 'Menunggu Konfirmasi') {
                throw ValidationException::withMessages([
                    'status' => 'Laporan kehilangan ini sudah ditinjau atau tidak valid.',
                ]);
            }

            $borrow = Transaction::whereKey($report->borrow_transaction_id)
                ->where('type', 'borrow')
                ->lockForUpdate()
                ->first();

            if (!$borrow || $borrow->status !== 'Dipinjam') {
                throw ValidationException::withMessages([
                    'status' => 'Pinjaman terkait sudah tidak aktif; laporan tidak dapat ditolak.',
                ]);
            }

            $reviewNote = 'Laporan kehilangan ditolak oleh ' . Auth::user()->name . ' pada ' . now('Asia/Jakarta')->format('d-m-Y H:i:s') . '.';
            $report->update([
                'status' => 'Ditolak',
                'notes' => trim($report->notes . ' | ' . $reviewNote),
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Laporan ditolak. Pinjaman tetap berstatus aktif.');
    }

    public function transferForm()
    {
        return view('transactions.transfer', [
            'items' => Item::with('location')
                ->where('is_active', true)
                ->where('status', 'Tersedia')
                ->whereIn('condition', ['Baik', 'Perlu Perbaikan'])
                ->whereDoesntHave('transactions', fn ($query) => $query
                    ->where('type', 'borrow')
                    ->where('status', 'Dipinjam'))
                ->orderBy('name')
                ->get(),
            'locations' => Location::where('is_active', true)->where('name', '!=', 'Gudang')->orderBy('name')->get(),
            'staff' => Staff::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function transferStore(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', Rule::exists('items', 'id')->where('is_active', true)],
            'to_location' => ['required', Rule::exists('locations', 'name')->where(fn ($query) => $query->where('is_active', true)->where('name', '!=', 'Gudang'))],
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

        DB::transaction(function () use ($validated, $fullDateTime) {
            $item = Item::with('location')->lockForUpdate()->findOrFail($validated['item_id']);

            $hasActiveBorrow = $item->transactions()
                ->where('type', 'borrow')
                ->where('status', 'Dipinjam')
                ->exists();
            if ($item->status !== 'Tersedia' || $hasActiveBorrow) {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang yang sedang dipinjam atau tidak tersedia tidak dapat dipindahkan.',
                ]);
            }

            if ($item->condition === 'Rusak') {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang rusak hanya dapat disimpan di Gudang.',
                ]);
            }

            Transaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'transfer',
                'status' => 'Dipindahkan',
                'from_location' => $item->location?->name ?? 'Belum ditentukan',
                'to_location' => $validated['to_location'],
                'from_person' => $validated['transferred_by'],
                'to_person' => $validated['transferred_by'],
                'quantity' => $item->quantity,
                'transaction_date' => $fullDateTime,
                'condition' => $item->condition,
                'notes' => ($validated['reason'] ?? '') . (!empty($validated['notes']) ? ' - ' . $validated['notes'] : ''),
            ]);

            $item->update([
                'location_id' => Location::where('name', $validated['to_location'])->firstOrFail()->id,
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Perpindahan barang berhasil dicatat.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['item', 'user', 'returnTransaction.user', 'lossReports.user']);

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

            if (!$item || !$item->is_active || $item->status !== 'Tersedia' || $item->condition === 'Rusak' || $item->quantity < $borrow->quantity) {
                throw ValidationException::withMessages(['status' => 'Barang tidak tersedia atau stok tidak cukup untuk persetujuan ini.']);
            }

            $destination = Location::where('name', $borrow->to_location)
                ->where('is_active', true)
                ->where('name', '!=', 'Gudang')
                ->first();

            if (!$destination) {
                throw ValidationException::withMessages(['status' => 'Lokasi tujuan sudah tidak aktif. Ajukan pinjaman dengan lokasi aktif.']);
            }

            $borrow->update(['status' => 'Dipinjam']);
            $item->update([
                'status' => 'Dipinjam',
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
            abort_unless(Auth::user()->role === 'admin', 403);

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
            ->whereIn('type', ['borrow', 'transfer'])
            ->orderByDesc('transaction_date');

        if ($request->filled('type')) {
            if ($request->type === 'borrow') {
                $query->where('type', 'borrow')->whereIn('status', ['Dipinjam', 'Menunggu Persetujuan']);
            } elseif ($request->type === 'return') {
                $query->where('type', 'borrow')->where('status', 'Dikembalikan');
            } elseif ($request->type === 'lost') {
                $query->where('type', 'borrow')->where('status', 'Hilang');
            } elseif ($request->type === 'found') {
                $query->where('type', 'borrow')->where('status', 'Ditemukan');
            } elseif ($request->type === 'loss') {
                $query->where('type', 'borrow')->whereHas('lossReports', fn ($reportQuery) => $reportQuery->where('status', 'Menunggu Konfirmasi'));
            } elseif ($request->type === 'transfer') {
                $query->where('type', 'transfer')->where('status', 'Dipindahkan');
            }
        }

        if ($request->filled('user_id')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->where('user_id', $request->user_id));
            } elseif ($request->type === 'loss') {
                $query->whereHas('lossReports', fn ($reportQuery) => $reportQuery->where('user_id', $request->user_id));
            } else {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->filled('date')) {
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereDate('transaction_date', $request->date));
            } elseif ($request->type === 'loss') {
                $query->whereHas('lossReports', fn ($reportQuery) => $reportQuery->whereDate('transaction_date', $request->date));
            } else {
                $query->whereDate('transaction_date', $request->date);
            }
        }

        if ($request->filled('time')) {
            $timePrefix = substr($request->time, 0, 5);
            if ($request->type === 'return') {
                $query->whereHas('returnTransaction', fn ($returnQuery) => $returnQuery->whereTime('transaction_date', 'like', "{$timePrefix}%"));
            } elseif ($request->type === 'loss') {
                $query->whereHas('lossReports', fn ($reportQuery) => $reportQuery->whereTime('transaction_date', 'like', "{$timePrefix}%"));
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
                'Durasi Peminjaman (Hari)',
                'Tanggal Jatuh Tempo',
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
                    $t->loan_duration_days ?? '-',
                    $t->formatted_due_date,
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
