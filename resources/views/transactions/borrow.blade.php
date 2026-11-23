@extends('layouts.app')

@section('title', 'Ambil Barang')

@section('content')
    <style>
        .borrow-item-menu { background: var(--card-bg, #fff); color: var(--text); border: 1px solid var(--border, #dee2e6); border-radius: 8px; z-index: 1050; }
        .borrow-item-option { position: relative; padding: .75rem 5rem .75rem .75rem; border-radius: 6px; cursor: pointer; overflow-wrap: anywhere; }
        .borrow-item-option:hover, .borrow-item-option.is-active { background: var(--primary-soft); color: var(--text); }
        .borrow-item-option[aria-selected="true"] { border-left: 3px solid var(--primary); }
        .borrow-item-option[aria-selected="true"]::after { content: "Dipilih"; position: absolute; top: 50%; right: .75rem; transform: translateY(-50%); color: var(--primary); font-size: .8rem; font-weight: 700; }
        body.dark-mode .borrow-item-option:hover, body.dark-mode .borrow-item-option.is-active { background: var(--primary-soft); color: var(--text); }
        .borrow-selected-list { margin-top: .5rem; border: 1px solid color-mix(in srgb, var(--border, #dee2e6) 38%, transparent); border-radius: 8px; overflow-x: auto; background: transparent; }
        .borrow-selected-list[hidden] { display: none; }
        .borrow-selected-list-title { padding: .5rem .75rem; border-bottom: 1px solid color-mix(in srgb, var(--border, #dee2e6) 38%, transparent); color: var(--text); font-size: .875rem; font-weight: 400; }
        .borrow-selected-table { width: max-content; min-width: 1104px; margin: 0; table-layout: fixed; color: var(--text); --bs-table-bg: transparent; --bs-table-color: var(--text); --bs-table-border-color: color-mix(in srgb, var(--border, #dee2e6) 32%, transparent); }
        .borrow-selected-table th, .borrow-selected-table td { padding: .45rem .4rem; overflow-wrap: break-word; vertical-align: middle; background: transparent !important; color: var(--text) !important; }
        .borrow-selected-table th { font-size: .75rem; font-weight: 400 !important; }
        .borrow-selected-table td { font-size: .9rem; }
        .borrow-selected-table .borrow-selected-number { width: 4rem; }
        .borrow-selected-table .borrow-selected-name { width: 12rem; }
        .borrow-selected-table .borrow-selected-code { width: 8rem; }
        .borrow-selected-table .borrow-selected-condition { width: 8rem; }
        .borrow-selected-table .borrow-selected-location { width: 10rem; }
        .borrow-selected-table .borrow-selected-purpose { width: 10rem; }
        .borrow-selected-table .borrow-selected-due-date { width: 13rem; }
        .borrow-selected-table .borrow-selected-remove { width: 4rem; text-align: center; }
        .borrow-selected-table th { white-space: nowrap; }
        .borrow-selected-table select, .borrow-selected-table input { min-width: 0; width: 100%; padding: .375rem .5rem; }
        .borrow-selected-remove-button { width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; padding: .25rem; }
        .borrow-transaction-time { font-variant-numeric: tabular-nums; line-height: 1.5; }
        .borrow-transaction-time::-webkit-datetime-edit-fields-wrapper { align-items: baseline; }
        .borrow-transaction-time::-webkit-datetime-edit-hour-field,
        .borrow-transaction-time::-webkit-datetime-edit-minute-field,
        .borrow-transaction-time::-webkit-datetime-edit-second-field { font: inherit; line-height: inherit; }
    </style>
    <div class="card p-4">
        <h4 class="mb-3">Ambil / Pinjam Barang</h4>
        <form action="{{ route('transactions.borrow') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="borrowStaff">Dipinjam oleh (staf perpustakaan)</label>
                    <div class="position-relative" id="borrowStaffPicker">
                        <button type="button" id="borrowStaffToggle" class="form-select text-start text-wrap" aria-haspopup="listbox" aria-expanded="false" aria-controls="borrowStaffList" hidden>Pilih staf</button>
                        <div id="borrowStaffMenu" class="borrow-item-menu position-absolute w-100 p-2 shadow mt-1" hidden>
                            <div class="d-flex gap-2 mb-2">
                                <input type="search" id="borrowStaffSearch" class="form-control" placeholder="Cari nama staf..." aria-label="Cari nama staf perpustakaan" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="borrowStaffList" autocomplete="off">
                                <button type="button" id="borrowStaffDone" class="btn btn-secondary flex-shrink-0" aria-label="Selesai memilih staf">Selesai</button>
                            </div>
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
                <div class="col-12">
                    <label class="form-label" for="borrowItem">Nama Barang (bisa pilih beberapa)</label>
                    <div class="position-relative" id="borrowItemPicker">
                        <button type="button" id="borrowItemToggle" class="form-select text-start text-wrap" aria-haspopup="listbox" aria-expanded="false" aria-controls="borrowItemList" hidden>Pilih barang</button>
                        <div id="borrowItemMenu" class="borrow-item-menu position-absolute w-100 p-2 shadow mt-1" hidden>
                            <div class="d-flex gap-2 mb-2">
                                <input type="search" id="borrowItemSearch" class="form-control" placeholder="Cari nama atau kode barang..." aria-label="Cari nama atau kode barang" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="borrowItemList" autocomplete="off">
                                <button type="button" id="borrowItemDone" class="btn btn-secondary flex-shrink-0" aria-label="Selesai memilih barang">Selesai</button>
                            </div>
                            <div id="borrowItemList" role="listbox" aria-label="Barang tersedia" aria-multiselectable="true" style="max-height: 260px; overflow-y: auto;">
                                @foreach($items as $item)
                                    <div id="borrowItemOption{{ $item->id }}" class="borrow-item-option" role="option" aria-selected="false" data-value="{{ $item->id }}" data-search="{{ $item->name }} {{ $item->brand }} {{ $item->code }}" data-label="{{ $item->name }}{{ $item->brand ? ' — ' . $item->brand : '' }} — {{ $item->code }}" data-name="{{ $item->name }}" data-brand="{{ $item->brand ?: '-' }}" data-code="{{ $item->code }}" data-condition="{{ $item->condition ?: '-' }}" data-location="{{ old('item_locations.' . $item->id, '') }}" data-purpose="{{ old('item_purposes.' . $item->id, '') }}" data-due-datetime="{{ old('item_due_datetimes.' . $item->id, now()->addDays(2)->format('Y-m-d\TH:i')) }}" data-detail="Kondisi: {{ $item->condition ?: '-' }} | Asal: {{ $item->location?->name ?? '-' }}" data-stock="{{ $item->quantity }}" data-unit="{{ $item->unit ?? 'unit' }}">
                                        <div class="fw-semibold">{{ $item->name }}@if($item->brand) <span class="text-secondary">· {{ $item->brand }}</span>@endif</div>
                                        <div class="small text-secondary d-flex flex-wrap gap-2 mt-1">
                                            <span>{{ $item->code }}</span><span>· Kondisi: {{ $item->condition ?: '-' }}</span><span>· Asal: {{ $item->location?->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small id="borrowItemEmpty" class="text-secondary" role="status" hidden>Barang tidak ditemukan.</small>
                        </div>
                    </div>
                    <select id="borrowItem" name="item_ids[]" class="form-select" multiple required>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-search="{{ $item->code }} {{ $item->name }} {{ $item->brand }}" @selected(in_array((string) $item->id, array_map('strval', (array) old('item_ids', [])), true)) data-stock="{{ $item->quantity }}" data-unit="{{ $item->unit ?? 'unit' }}">
                                {{ $item->code }} &ensp; | &ensp; {{ $item->name }}{{ $item->brand ? ' · ' . $item->brand : '' }} &ensp; | &ensp; Kondisi: {{ $item->condition ?: '-' }} &ensp; | &ensp; Asal: {{ $item->location?->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    <small id="borrowItemDetail" class="text-secondary" hidden></small>
                    <div id="borrowItemSelectedList" class="borrow-selected-list" aria-live="polite" hidden>
                        <div class="borrow-selected-list-title">Daftar barang yang diambil</div>
                        <table class="table table-sm borrow-selected-table">
                            <thead>
                                <tr><th class="borrow-selected-number">No.</th><th class="borrow-selected-name">Nama barang</th><th class="borrow-selected-code">Kode</th><th class="borrow-selected-condition">Kondisi</th><th class="borrow-selected-location">Ke lokasi</th><th class="borrow-selected-purpose">Keperluan</th><th class="borrow-selected-due-date">Tanggal jatuh tempo</th><th class="borrow-selected-remove">Hapus</th></tr>
                            </thead>
                            <tbody id="borrowItemSelectedRows"></tbody>
                        </table>
                    </div>
                    <select id="borrowLocationOptions" class="d-none" aria-hidden="true" tabindex="-1">
                        <option value="">Pilih lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->name }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <small id="borrowItemError" class="text-danger" role="alert" hidden>Pilih barang terlebih dahulu.</small>
                    @error('item_ids')<div class="text-danger small">{{ $message }}</div>@enderror
                    @error('item_locations')<div class="text-danger small">{{ $message }}</div>@enderror
                    @error('item_purposes')<div class="text-danger small">{{ $message }}</div>@enderror
                    @foreach($errors->getMessages() as $errorKey => $messages)
                        @if($errorKey === 'item_due_datetimes' || \Illuminate\Support\Str::startsWith($errorKey, 'item_due_datetimes.'))
                            <div class="text-danger small">{{ $messages[0] }}</div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="borrowTransactionTime">Waktu Transaksi</label>
                    <input id="borrowTransactionTime" type="time" name="transaction_time" class="form-control borrow-transaction-time" value="{{ old('transaction_time', now()->format('H:i:s')) }}" step="1" required>
                    <div class="form-text">Format 24 jam (HH:mm:ss)</div>
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
            const doneButton = document.getElementById(id + 'Done');
            const select = document.getElementById(id);
            const empty = document.getElementById(id + 'Empty');
            const detail = document.getElementById(id + 'Detail');
            const error = document.getElementById(id + 'Error');
            const multiple = select.multiple;
            const selectedList = multiple ? document.getElementById(id + 'SelectedList') : null;
            const selectedRows = multiple ? document.getElementById(id + 'SelectedRows') : null;
            const options = [...picker.querySelectorAll('[role="option"]')];
            const locationTemplate = multiple ? document.getElementById('borrowLocationOptions') : null;
            const selectedLocations = multiple
                ? new Map(options.map(option => [option.dataset.value, option.dataset.location || '']))
                : null;
            const selectedPurposes = multiple
                ? new Map(options.map(option => [option.dataset.value, option.dataset.purpose || '']))
                : null;
            const selectedDueDateTimes = multiple
                ? new Map(options.map(option => [option.dataset.value, option.dataset.dueDatetime || '']))
                : null;
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
                if (multiple) {
                    const selectedValues = new Set([...select.selectedOptions].map(option => option.value));
                    options.sort((first, second) => Number(selectedValues.has(second.dataset.value)) - Number(selectedValues.has(first.dataset.value)));
                    options.forEach(option => option.parentElement.appendChild(option));
                }
                const selected = multiple
                    ? options.filter(option => [...select.selectedOptions].some(selectedOption => selectedOption.value === option.dataset.value))
                    : options.filter(option => option.dataset.value === select.value);
                toggle.textContent = selected.length
                    ? (multiple ? `${selected.length} unit dipilih — pilih lagi` : selected[0].dataset.label)
                    : placeholder;
                if (detail) {
                    if (multiple) {
                        detail.hidden = true;
                        selectedRows.replaceChildren();
                        selected.forEach((option, index) => {
                            const row = document.createElement('tr');
                            const numberCell = document.createElement('td');
                            numberCell.className = 'borrow-selected-number';
                            numberCell.textContent = String(index + 1);
                            row.appendChild(numberCell);
                            const nameCell = document.createElement('td');
                            nameCell.className = 'borrow-selected-name';
                            const itemName = document.createElement('div');
                            itemName.textContent = option.dataset.name;
                            nameCell.appendChild(itemName);
                            if (option.dataset.brand && option.dataset.brand !== '-') {
                                const brandName = document.createElement('small');
                                brandName.className = 'd-block text-secondary';
                                brandName.textContent = option.dataset.brand;
                                nameCell.appendChild(brandName);
                            }
                            row.appendChild(nameCell);
                            const codeCell = document.createElement('td');
                            codeCell.className = 'borrow-selected-code';
                            codeCell.textContent = option.dataset.code;
                            row.appendChild(codeCell);
                            const conditionCell = document.createElement('td');
                            conditionCell.className = 'borrow-selected-condition';
                            conditionCell.textContent = option.dataset.condition;
                            row.appendChild(conditionCell);
                            const locationCell = document.createElement('td');
                            locationCell.className = 'borrow-selected-location';
                            const locationSelect = document.createElement('select');
                            locationSelect.name = `item_locations[${option.dataset.value}]`;
                            locationSelect.className = 'form-select form-select-sm';
                            locationSelect.required = true;
                            locationSelect.setAttribute('aria-label', `Lokasi tujuan untuk ${option.dataset.name}`);
                            [...locationTemplate.options].forEach(templateOption => {
                                const locationOption = document.createElement('option');
                                locationOption.value = templateOption.value;
                                locationOption.textContent = templateOption.textContent;
                                locationSelect.appendChild(locationOption);
                            });
                            locationSelect.value = selectedLocations.get(option.dataset.value) || '';
                            locationSelect.addEventListener('change', () => selectedLocations.set(option.dataset.value, locationSelect.value));
                            locationCell.appendChild(locationSelect);
                            row.appendChild(locationCell);
                            const purposeCell = document.createElement('td');
                            purposeCell.className = 'borrow-selected-purpose';
                            const purposeInput = document.createElement('input');
                            purposeInput.type = 'text';
                            purposeInput.name = `item_purposes[${option.dataset.value}]`;
                            purposeInput.className = 'form-control form-control-sm';
                            purposeInput.value = selectedPurposes.get(option.dataset.value) || '';
                            purposeInput.placeholder = 'Keperluan';
                            purposeInput.setAttribute('aria-label', `Keperluan untuk ${option.dataset.name}`);
                            purposeInput.addEventListener('input', () => selectedPurposes.set(option.dataset.value, purposeInput.value));
                            purposeCell.appendChild(purposeInput);
                            row.appendChild(purposeCell);
                            const dueDateCell = document.createElement('td');
                            dueDateCell.className = 'borrow-selected-due-date';
                            const dueDateInput = document.createElement('input');
                            dueDateInput.type = 'datetime-local';
                            dueDateInput.name = `item_due_datetimes[${option.dataset.value}]`;
                            dueDateInput.className = 'form-control form-control-sm';
                            dueDateInput.value = selectedDueDateTimes.get(option.dataset.value) || '';
                            dueDateInput.required = true;
                            dueDateInput.setAttribute('aria-label', `Tanggal jatuh tempo untuk ${option.dataset.name}`);
                            dueDateInput.addEventListener('input', () => selectedDueDateTimes.set(option.dataset.value, dueDateInput.value));
                            dueDateCell.appendChild(dueDateInput);
                            row.appendChild(dueDateCell);
                            const removeCell = document.createElement('td');
                            removeCell.className = 'borrow-selected-remove';
                            const removeButton = document.createElement('button');
                            removeButton.type = 'button';
                            removeButton.className = 'btn btn-sm btn-outline-danger borrow-selected-remove-button';
                            removeButton.setAttribute('aria-label', `Hapus ${option.dataset.name} dari daftar barang`);
                            removeButton.title = `Hapus ${option.dataset.name}`;
                            removeButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 4h10M6 4V2.75h4V4m2 0-.55 9H4.55L4 4m2.5 2v5m3-5v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                            removeButton.addEventListener('click', () => {
                                const selectOption = [...select.options].find(selectOption => selectOption.value === option.dataset.value);
                                if (selectOption) selectOption.selected = false;
                                select.dispatchEvent(new Event('change', { bubbles: true }));
                                error.hidden = select.selectedOptions.length > 0;
                                toggle.removeAttribute('aria-invalid');
                                renderSelection();
                            });
                            removeCell.appendChild(removeButton);
                            row.appendChild(removeCell);
                            selectedRows.appendChild(row);
                        });
                        selectedList.hidden = selected.length === 0;
                    } else {
                        detail.textContent = selected[0]?.dataset.detail || '';
                        detail.hidden = selected.length === 0;
                    }
                }
                options.forEach(option => option.setAttribute('aria-selected', String(selected.includes(option))));
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
            doneButton.addEventListener('click', () => close(true));
            const choose = option => {
                if (multiple) {
                    const selectOption = [...select.options].find(selectOption => selectOption.value === option.dataset.value);
                    if (selectOption) selectOption.selected = !selectOption.selected;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    error.hidden = select.selectedOptions.length > 0;
                    toggle.removeAttribute('aria-invalid');
                    renderSelection();
                    return;
                }
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
