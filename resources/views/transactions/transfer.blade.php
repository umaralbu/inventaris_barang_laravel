@extends('layouts.app')

@section('title', 'Perpindahan Barang')

@section('content')
    <style>
        .transfer-item-picker { position: relative; }
        .transfer-item-results { position: absolute; z-index: 1050; top: calc(100% + .25rem); width: 100%; max-height: 260px; overflow-y: auto; padding: .35rem; border: 1px solid var(--border, #dee2e6); border-radius: 8px; background: var(--card-bg, #fff); color: var(--text); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12); }
        .transfer-item-results[hidden], .transfer-item-option[hidden] { display: none; }
        .transfer-item-option { padding: .55rem .65rem; border-radius: 6px; cursor: pointer; }
        .transfer-item-option:hover, .transfer-item-option:focus { outline: none; background: var(--primary-soft); }
    </style>
    <div class="card p-4">
        <h4 class="mb-3">Perpindahan Barang</h4>
        <form action="{{ route('transactions.transfer') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="transferItemSearch">Barang</label>
                    <div class="transfer-item-picker">
                        <input type="search" id="transferItemSearch" class="form-control" placeholder="Ketik nama, merek, atau kode barang..." role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="transferItemList" autocomplete="off">
                        <div id="transferItemList" class="transfer-item-results" role="listbox" aria-label="Pilih barang" hidden>
                            @foreach($items as $item)
                                <div class="transfer-item-option" role="option" tabindex="-1" data-value="{{ $item->id }}" data-label="{{ $item->code }} - {{ $item->name }}{{ $item->brand ? ' - ' . $item->brand : '' }}" data-search="{{ $item->code }} {{ $item->name }} {{ $item->brand }}" data-location="{{ $item->location?->name ?? '' }}">
                                    <div class="fw-semibold">{{ $item->code }} - {{ $item->name }}@if($item->brand) <span class="text-secondary">· {{ $item->brand }}</span>@endif</div>
                                    <div class="small text-secondary">Dari lokasi: {{ $item->location?->name ?? 'Belum ditentukan' }}</div>
                                </div>
                            @endforeach
                            <div id="transferItemEmpty" class="small text-secondary p-2" role="status" hidden>Barang tidak ditemukan.</div>
                        </div>
                        <select id="transferItem" name="item_id" class="d-none">
                            <option value="">Pilih barang</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" @selected((string) old('item_id') === (string) $item->id)>{{ $item->code }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('item_id')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dipindahkan oleh</label>
                    <select name="transferred_by" class="form-select" data-searchable-select required>
                        <option value="">Pilih staf</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->name }}" @selected(old('transferred_by') === $staffMember->name)>{{ $staffMember->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="transferFromLocation">Dari lokasi</label>
                    <input type="text" id="transferFromLocation" class="form-control" value="" placeholder="Otomatis dari lokasi barang" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ke lokasi</label>
                    <select name="to_location" class="form-select" data-searchable-select>
                        @foreach($locations as $location)
                            <option value="{{ $location->name }}" @selected(old('to_location') === $location->name)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Perpindahan</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Waktu Transaksi</label>
                    <input type="time" name="transaction_time" class="form-control" value="{{ old('transaction_time', now()->format('H:i')) }}" step="1" required>
                    <div class="form-text">Format 24 jam (HH:mm)</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alasan</label>
                    <input type="text" name="reason" class="form-control">
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
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('transferItemSearch');
            const list = document.getElementById('transferItemList');
            const select = document.getElementById('transferItem');
            const fromLocation = document.getElementById('transferFromLocation');
            const empty = document.getElementById('transferItemEmpty');
            const options = [...list.querySelectorAll('[role="option"]')];

            const filterItems = () => {
                const query = search.value.trim().toLocaleLowerCase('id');
                let visibleCount = 0;
                options.forEach(option => {
                    option.hidden = !option.dataset.search.toLocaleLowerCase('id').includes(query);
                    if (!option.hidden) visibleCount++;
                });
                empty.hidden = visibleCount > 0;
            };
            const setSelectedItem = option => {
                select.value = option.dataset.value;
                search.value = option.dataset.label;
                fromLocation.value = option.dataset.location || 'Belum ditentukan';
                search.setAttribute('aria-expanded', 'false');
                list.hidden = true;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            };

            const selectedOption = options.find(option => option.dataset.value === select.value);
            if (selectedOption) setSelectedItem(selectedOption);

            search.addEventListener('focus', () => {
                filterItems();
                list.hidden = false;
                search.setAttribute('aria-expanded', 'true');
            });
            search.addEventListener('input', () => {
                select.value = '';
                fromLocation.value = '';
                filterItems();
                list.hidden = false;
                search.setAttribute('aria-expanded', 'true');
            });
            options.forEach(option => option.addEventListener('click', () => setSelectedItem(option)));
            search.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    list.hidden = true;
                    search.setAttribute('aria-expanded', 'false');
                }
                if (event.key === 'Enter' && !list.hidden) {
                    const match = options.find(option => !option.hidden);
                    if (match) {
                        event.preventDefault();
                        setSelectedItem(match);
                    }
                }
            });
            document.addEventListener('click', event => {
                if (!event.target.closest('.transfer-item-picker')) {
                    list.hidden = true;
                    search.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
@endsection
