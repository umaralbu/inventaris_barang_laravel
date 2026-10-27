@extends('layouts.app')

@section('title', 'Ambil Barang')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Ambil / Pinjam Barang</h4>
        <form action="{{ route('transactions.borrow') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="borrowItem">Nama Barang</label>
                    <input type="search" id="borrowItemSearch" class="form-control mb-2" placeholder="Cari nama atau kode barang..." aria-label="Cari nama atau kode barang" autocomplete="off" hidden>
                    <select id="borrowItem" name="item_id" class="form-select" required>
                        <option value="">Pilih barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-search="{{ $item->code }} {{ $item->name }}" @selected((string) old('item_id') === (string) $item->id)>
                                {{ $item->code }} &ensp; | &ensp; {{ $item->name }} &ensp; | &ensp; Stok: {{ $item->quantity }} {{ $item->unit }} &ensp; | &ensp; Asal: {{ $item->location?->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    <small id="borrowItemEmpty" class="text-secondary" role="status" hidden>Barang tidak ditemukan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="borrowStaff">Dipinjam oleh (staf perpustakaan)</label>
                    <input type="search" id="borrowStaffSearch" class="form-control mb-2" placeholder="Cari nama staf..." aria-label="Cari nama staf perpustakaan" autocomplete="off" hidden>
                    <select id="borrowStaff" name="staff_id" class="form-select" required>
                        <option value="">Pilih staf</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" @selected((string) old('staff_id') === (string) $staffMember->id)>{{ $staffMember->name }}</option>
                        @endforeach
                    </select>
                    <small id="borrowStaffEmpty" class="text-secondary" role="status" hidden>Nama staf tidak ditemukan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dipinjam ke lokasi</label>
                    <select name="to_location" class="form-select" required>
                        <option value="">Pilih tujuan penggunaan</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->name }}" @selected(old('to_location') === $location->name)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Waktu Transaksi</label>
                    <input type="time" name="transaction_time" class="form-control" value="{{ old('transaction_time', now()->format('H:i')) }}" step="1" required>
                    <div class="form-text">Format 24 jam (HH:mm)</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keperluan</label>
                    <input type="text" name="purpose" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function setupSearch(id, placeholderText, emptyText) {
            const search = document.getElementById(id + 'Search');
            const select = document.getElementById(id);
            const empty = document.getElementById(id + 'Empty');
            const options = [...select.options].slice(1);
            let selectedValue = select.value;
            search.hidden = false;

            search.addEventListener('input', () => {
                const term = search.value.trim().toLocaleLowerCase('id');
                const matches = options.filter(option => (option.dataset.search || option.textContent).toLocaleLowerCase('id').includes(term));
                const placeholder = new Option(matches.length ? placeholderText : emptyText, '');
                select.replaceChildren(placeholder, ...matches);
                select.value = matches.some(option => option.value === selectedValue) ? selectedValue : '';
                empty.hidden = matches.length > 0;
            });
            select.addEventListener('change', () => {
                selectedValue = select.value;
            });
            }
            setupSearch('borrowItem', 'Pilih barang', 'Barang tidak ditemukan');
            setupSearch('borrowStaff', 'Pilih staf', 'Nama staf tidak ditemukan');
        });
    </script>
@endsection
