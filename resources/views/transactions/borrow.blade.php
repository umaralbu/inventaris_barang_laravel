@extends('layouts.app')

@section('title', 'Ambil Barang')

@section('content')
    <style>
        .borrow-item-menu { background: var(--card-bg, #fff); color: var(--text); border: 1px solid var(--border, #dee2e6); border-radius: 8px; z-index: 1050; }
        .borrow-item-option { padding: .75rem; border-radius: 6px; cursor: pointer; overflow-wrap: anywhere; }
        .borrow-item-option:hover, .borrow-item-option.is-active { background: #e7f1ff; color: #212529; }
        .borrow-item-option[aria-selected="true"] { border-left: 3px solid #0d6efd; }
        body.dark-mode .borrow-item-option:hover, body.dark-mode .borrow-item-option.is-active { background: #26384f; color: #fff; }
    </style>
    <div class="card p-4">
        <h4 class="mb-3">Ambil / Pinjam Barang</h4>
        <form action="{{ route('transactions.borrow') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="borrowItem">Nama Barang</label>
                    <div class="position-relative" id="borrowItemPicker">
                        <button type="button" id="borrowItemToggle" class="form-select text-start text-wrap" aria-haspopup="listbox" aria-expanded="false" aria-controls="borrowItemList" hidden>Pilih barang</button>
                        <div id="borrowItemMenu" class="borrow-item-menu position-absolute w-100 p-2 shadow mt-1" hidden>
                            <input type="search" id="borrowItemSearch" class="form-control mb-2" placeholder="Cari nama atau kode barang..." aria-label="Cari nama atau kode barang" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="borrowItemList" autocomplete="off">
                            <div id="borrowItemList" role="listbox" aria-label="Barang tersedia" style="max-height: 260px; overflow-y: auto;">
                                @foreach($items as $item)
                                    <div id="borrowItemOption{{ $item->id }}" class="borrow-item-option" role="option" aria-selected="false" data-value="{{ $item->id }}" data-search="{{ $item->name }} {{ $item->code }}" data-label="{{ $item->name }} — {{ $item->code }}" data-detail="Stok: {{ $item->quantity }} {{ $item->unit }} | Asal: {{ $item->location?->name ?? '-' }}">
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <div class="small text-secondary d-flex flex-wrap gap-2 mt-1">
                                            <span>{{ $item->code }}</span><span>· Stok: {{ $item->quantity }} {{ $item->unit }}</span><span>· Asal: {{ $item->location?->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small id="borrowItemEmpty" class="text-secondary" role="status" hidden>Barang tidak ditemukan.</small>
                        </div>
                    </div>
                    <select id="borrowItem" name="item_id" class="form-select" required>
                        <option value="">Pilih barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-search="{{ $item->code }} {{ $item->name }}" @selected((string) old('item_id') === (string) $item->id)>
                                {{ $item->code }} &ensp; | &ensp; {{ $item->name }} &ensp; | &ensp; Stok: {{ $item->quantity }} {{ $item->unit }} &ensp; | &ensp; Asal: {{ $item->location?->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    <small id="borrowItemDetail" class="text-secondary" hidden></small>
                    <small id="borrowItemError" class="text-danger" role="alert" hidden>Pilih barang terlebih dahulu.</small>
                    @error('item_id')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="borrowStaff">Dipinjam oleh (staf perpustakaan)</label>
                    <div class="position-relative" id="borrowStaffPicker">
                        <button type="button" id="borrowStaffToggle" class="form-select text-start text-wrap" aria-haspopup="listbox" aria-expanded="false" aria-controls="borrowStaffList" hidden>Pilih staf</button>
                        <div id="borrowStaffMenu" class="borrow-item-menu position-absolute w-100 p-2 shadow mt-1" hidden>
                            <input type="search" id="borrowStaffSearch" class="form-control mb-2" placeholder="Cari nama staf..." aria-label="Cari nama staf perpustakaan" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="borrowStaffList" autocomplete="off">
                            <div id="borrowStaffList" role="listbox" aria-label="Staf perpustakaan aktif" style="max-height: 260px; overflow-y: auto;">
                                @foreach($staff as $staffMember)
                                    <div id="borrowStaffOption{{ $staffMember->id }}" class="borrow-item-option" role="option" aria-selected="false" data-value="{{ $staffMember->id }}" data-search="{{ $staffMember->name }}" data-label="{{ $staffMember->name }}">
                                        <div class="fw-semibold">{{ $staffMember->name }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <small id="borrowStaffEmpty" class="text-secondary" role="status" hidden>Nama staf tidak ditemukan.</small>
                        </div>
                    </div>
                    <select id="borrowStaff" name="staff_id" class="form-select" required>
                        <option value="">Pilih staf</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" @selected((string) old('staff_id') === (string) $staffMember->id)>{{ $staffMember->name }}</option>
                        @endforeach
                    </select>
                    <small id="borrowStaffError" class="text-danger" role="alert" hidden>Pilih staf terlebih dahulu.</small>
                    @error('staff_id')<div class="text-danger small">{{ $message }}</div>@enderror
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
            function setupPicker(id, placeholder) {
            const picker = document.getElementById(id + 'Picker');
            const toggle = document.getElementById(id + 'Toggle');
            const menu = document.getElementById(id + 'Menu');
            const search = document.getElementById(id + 'Search');
            const select = document.getElementById(id);
            const empty = document.getElementById(id + 'Empty');
            const detail = document.getElementById(id + 'Detail');
            const error = document.getElementById(id + 'Error');
            const options = [...picker.querySelectorAll('[role="option"]')];
            let active = -1;
            let matches = options;
            const highlight = index => {
                active = index;
                options.forEach(option => option.classList.remove('is-active'));
                const option = matches[active];
                if (option) {
                    option.classList.add('is-active');
                    search.setAttribute('aria-activedescendant', option.id);
                    option.scrollIntoView({ block: 'nearest' });
                } else search.removeAttribute('aria-activedescendant');
            };
            const renderSelection = () => {
                const selected = options.find(option => option.dataset.value === select.value);
                toggle.textContent = selected?.dataset.label || placeholder;
                if (detail) {
                    detail.textContent = selected?.dataset.detail || '';
                    detail.hidden = !selected;
                }
                options.forEach(option => option.setAttribute('aria-selected', String(option === selected)));
            };
            const filter = () => {
                const term = search.value.trim().toLocaleLowerCase('id');
                matches = options.filter(option => {
                    option.hidden = !option.dataset.search.toLocaleLowerCase('id').includes(term);
                    return !option.hidden;
                });
                empty.hidden = matches.length > 0;
                highlight(-1);
            };
            const close = (restoreFocus = false) => {
                menu.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
                search.setAttribute('aria-expanded', 'false');
                if (restoreFocus) toggle.focus();
            };
            const open = () => {
                search.value = '';
                menu.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');
                search.setAttribute('aria-expanded', 'true');
                filter();
                search.focus();
            };
            const choose = option => {
                select.value = option.dataset.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                error.hidden = true;
                toggle.removeAttribute('aria-invalid');
                renderSelection();
                close(true);
            };
            toggle.hidden = false;
            select.hidden = true;
            document.querySelector('label[for="' + id + '"]').htmlFor = id + 'Toggle';
            renderSelection();
            toggle.addEventListener('click', () => menu.hidden ? open() : close());
            toggle.addEventListener('keydown', event => {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    open();
                    highlight(event.key === 'ArrowDown' ? 0 : matches.length - 1);
                }
            });
            search.addEventListener('input', filter);
            search.addEventListener('keydown', event => {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (matches.length) highlight(event.key === 'ArrowDown' ? (active + 1) % matches.length : (active <= 0 ? matches.length - 1 : active - 1));
                }
                if (event.key === 'Enter') {
                    event.preventDefault();
                    const option = matches[active] || matches[0];
                    if (option) choose(option);
                }
            });
            options.forEach(option => {
                option.addEventListener('mousedown', event => event.preventDefault());
                option.addEventListener('click', () => choose(option));
            });
            picker.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    close(true);
                }
            });
            picker.addEventListener('focusout', event => {
                if (!picker.contains(event.relatedTarget)) close();
            });
            document.addEventListener('click', event => {
                if (!picker.contains(event.target)) close();
            });
            select.addEventListener('invalid', event => {
                event.preventDefault();
                error.hidden = false;
                toggle.setAttribute('aria-invalid', 'true');
                open();
            });
            }
            setupPicker('borrowItem', 'Pilih barang');
            setupPicker('borrowStaff', 'Pilih staf');
        });
    </script>
@endsection
