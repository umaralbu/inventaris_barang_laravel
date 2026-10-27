@extends('layouts.app')

@section('title', 'Kembalikan Barang')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Kembalikan Barang</h4>
        <form action="{{ route('transactions.return') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Barang</label>
                    <select name="borrow_transaction_id" class="form-select" required>
                        @if($activeBorrowTransactions->isEmpty())
                            <option value="" selected disabled>Tidak ada pinjaman aktif yang dapat dikembalikan</option>
                        @else
                            <option value="">Pilih barang yang dipinjam</option>
                        @endif
                        @foreach($activeBorrowTransactions as $borrowTransaction)
                            <option value="{{ $borrowTransaction->id }}" data-borrower="{{ $borrowTransaction->to_person }}" @selected((string) old('borrow_transaction_id') === (string) $borrowTransaction->id || ($item && $item->id === $borrowTransaction->item_id))>
                                {{ $borrowTransaction->item->code }} - {{ $borrowTransaction->item->name }} - dipinjam oleh {{ $borrowTransaction->to_person }} ({{ $borrowTransaction->quantity }} {{ $borrowTransaction->item->unit }})
                            </option>
                        @endforeach
                    </select>
                    @if($activeBorrowTransactions->isEmpty() && $pendingBorrowCount > 0)
                        <div class="form-text text-warning">Ada {{ $pendingBorrowCount }} permintaan pinjaman yang masih menunggu persetujuan admin. Barang akan muncul di sini setelah disetujui.</div>
                    @elseif($activeBorrowTransactions->isEmpty())
                        <div class="form-text">Daftar ini hanya berisi pinjaman yang sudah disetujui dan belum dikembalikan.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dikembalikan oleh</label>
                    <select name="from_person" id="returnStaff" class="form-select" required>
                        <option value="">Pilih staf yang menyerahkan barang</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->name }}" @selected(old('from_person') === $staffMember->name)>{{ $staffMember->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tujuan / Lokasi pengembalian</label>
                    <select name="to_location" class="form-select">
                        @foreach(App\Models\Location::where('is_active', true)->orderBy('name')->get() as $location)
                            <option value="{{ $location->name }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Pengembalian</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Waktu Transaksi</label>
                    <input type="time" name="transaction_time" class="form-control" value="{{ old('transaction_time', now()->format('H:i')) }}" step="1" required>
                    <div class="form-text">Format 24 jam (HH:mm)</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kondisi setelah dikembalikan</label>
                    <select name="condition" class="form-select">
                        <option>Baik</option>
                        <option>Rusak</option>
                        <option>Perlu Perbaikan</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const borrowSelect = document.querySelector('[name="borrow_transaction_id"]');
            const staffSelect = document.getElementById('returnStaff');

            if (!borrowSelect || !staffSelect) return;

            const selectBorrower = function () {
                const borrower = borrowSelect.selectedOptions[0]?.dataset.borrower;
                if (borrower && Array.from(staffSelect.options).some((option) => option.value === borrower)) {
                    staffSelect.value = borrower;
                }
            };

            borrowSelect.addEventListener('change', selectBorrower);
            if (!staffSelect.value) selectBorrower();
        });
    </script>
@endsection
