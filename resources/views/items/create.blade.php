@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Tambah Barang</h4>
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" id="inventoryCodeLabel" for="inventoryCode0">Kode Inventaris</label>
                    <div class="position-relative" id="inventoryCodePicker">
                        <button type="button" id="inventoryCodeToggle" class="form-select text-start" aria-expanded="false" aria-controls="inventoryCodeMenu" hidden></button>
                        <div id="inventoryCodeMenu">
                        <div id="inventoryCodes" style="max-height: 260px; overflow-y: auto;">
                        @foreach(old('codes', [App\Models\Item::generateCode()]) as $code)
                            <input type="text" id="inventoryCode{{ $loop->index }}" name="codes[]" class="form-control mb-2" value="{{ $code }}" aria-label="Kode inventaris unit {{ $loop->iteration }}" maxlength="255" required>
                        @endforeach
                        </div>
                        </div>
                    </div>
                    <small class="text-secondary">Setiap unit memiliki kode berbeda. Klik kolom untuk melihat dan mengubah kode jika jumlah lebih dari 1.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="itemName">Nama Barang</label>
                    <div class="position-relative" id="itemNamePicker">
                        <input type="text" id="itemName" name="name" class="form-control" value="{{ old('name') }}" placeholder="Pilih atau tambahkan nama barang" autocomplete="off" maxlength="255" aria-expanded="false" aria-controls="itemNameMenu" required>
                        <div id="itemNameMenu" class="dropdown-menu w-100 p-2 shadow" style="z-index: 1050;" hidden>
                            <input type="search" id="itemNameSearch" class="form-control mb-2" placeholder="Cari nama barang..." aria-label="Cari nama barang" maxlength="255" autocomplete="off">
                            <button type="button" id="addItemName" class="btn btn-outline-primary w-100 text-start mb-2" hidden></button>
                            <p id="itemNameHint" class="small text-secondary mb-2">Ketik nama baru untuk menambahkannya.</p>
                            <div id="itemNameOptions" style="max-height: 240px; overflow-y: auto;">
                                @foreach($itemNames as $itemName)
                                    <button type="button" class="dropdown-item rounded text-wrap" data-item-name="{{ $itemName }}">{{ $itemName }}</button>
                                @endforeach
                            </div>
                            <p id="itemNameEmpty" class="small text-secondary mb-0" hidden>Nama barang belum tersedia.</p>
                        </div>
                    </div>
                    <small class="text-secondary">Nama baru akan tersimpan setelah menekan Simpan.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Pilih kategori</option>
                        @foreach(App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Pilih lokasi</option>
                        @foreach(App\Models\Location::where('is_active', true)->orderBy('name')->get() as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="itemBrand">Merek</label>
                    <div class="position-relative" id="itemBrandPicker">
                        <input type="text" id="itemBrand" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="Pilih atau tambahkan merek" autocomplete="off" maxlength="255" aria-expanded="false" aria-controls="itemBrandMenu">
                        <div id="itemBrandMenu" class="dropdown-menu w-100 p-2 shadow" style="z-index: 1050;" hidden>
                            <input type="search" id="itemBrandSearch" class="form-control mb-2" placeholder="Cari merek..." aria-label="Cari merek" maxlength="255" autocomplete="off">
                            <button type="button" id="addItemBrand" class="btn btn-outline-primary w-100 text-start mb-2" hidden></button>
                            <p id="itemBrandHint" class="small text-secondary mb-2">Ketik merek baru untuk menambahkannya.</p>
                            <div id="itemBrandOptions" style="max-height: 240px; overflow-y: auto;">
                                @foreach($itemBrands as $itemBrand)
                                    <button type="button" class="dropdown-item rounded text-wrap" data-item-brand="{{ $itemBrand }}">{{ $itemBrand }}</button>
                                @endforeach
                            </div>
                            <p id="itemBrandEmpty" class="small text-secondary mb-0" hidden>Merek belum tersedia.</p>
                        </div>
                    </div>
                    <small class="text-secondary">Merek baru akan tersimpan setelah menekan Simpan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Spesifikasi</label>
                    <textarea name="specification" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jumlah</label>
                    <input type="number" id="itemQuantity" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" max="1000" required>
                    <small class="text-secondary">Maksimal 1.000 unit per penyimpanan.</small>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="unit" class="form-control" value="pcs">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kondisi</label>
                    <select name="condition" class="form-select">
                        <option>Baik</option>
                        <option>Rusak</option>
                        <option>Perlu Perbaikan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option>Tersedia</option>
                        <option>Dipinjam</option>
                        <option>Dipindahkan</option>
                        <option>Hilang</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="purchase_price" class="form-control" step="0.01" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Foto Barang</label>
                    <input type="file" name="photo" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const quantity = document.getElementById('itemQuantity');
            const codeContainer = document.getElementById('inventoryCodes');
            const codePicker = document.getElementById('inventoryCodePicker');
            const codeToggle = document.getElementById('inventoryCodeToggle');
            const codeMenu = document.getElementById('inventoryCodeMenu');
            const codeLabel = document.getElementById('inventoryCodeLabel');
            const closeCodes = () => {
                if (codeContainer.children.length > 1) {
                    codeMenu.hidden = true;
                    codeMenu.classList.remove('show');
                }
                codeToggle.setAttribute('aria-expanded', 'false');
            };
            const openCodes = () => {
                codeMenu.hidden = false;
                codeMenu.classList.add('show');
                codeToggle.setAttribute('aria-expanded', 'true');
            };
            const updateCodeSummary = () => {
                const inputs = [...codeContainer.querySelectorAll('input')];
                codeToggle.textContent = (inputs[0]?.value || 'Kode inventaris') + ' — ' + inputs.length + ' kode';
            };
            codeToggle.addEventListener('click', () => {
                if (codeMenu.hidden) openCodes();
                else closeCodes();
            });
            codeContainer.addEventListener('input', updateCodeSummary);
            codeContainer.addEventListener('invalid', openCodes, true);
            codePicker.addEventListener('keydown', event => {
                if (event.key === 'Escape' && codeContainer.children.length > 1) {
                    closeCodes();
                    codeToggle.focus();
                }
            });
            codePicker.addEventListener('focusout', event => {
                if (!codePicker.contains(event.relatedTarget)) closeCodes();
            });
            document.addEventListener('click', event => {
                if (!codePicker.contains(event.target)) closeCodes();
            });
            const existingCodes = new Set(@json($existingCodes).map(code => code.toLocaleLowerCase('id')));
            const syncCodes = () => {
                const count = Number(quantity.value);
                if (!Number.isInteger(count) || count < 1 || count > 1000) return;
                while (codeContainer.children.length > count) codeContainer.lastElementChild.remove();
                const used = new Set(existingCodes);
                codeContainer.querySelectorAll('input').forEach(input => used.add(input.value.trim().toLocaleLowerCase('id')));
                let sequence = 1;
                while (codeContainer.children.length < count) {
                    let code;
                    do {
                        code = 'INV-' + String(sequence++).padStart(4, '0');
                    } while (used.has(code.toLocaleLowerCase('id')));
                    used.add(code.toLocaleLowerCase('id'));
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'codes[]';
                    input.id = 'inventoryCode' + codeContainer.children.length;
                    input.className = 'form-control mb-2';
                    input.value = code;
                    input.maxLength = 255;
                    input.required = true;
                    input.setAttribute('aria-label', 'Kode inventaris unit ' + (codeContainer.children.length + 1));
                    codeContainer.appendChild(input);
                }
                const multiple = count > 1;
                codeToggle.hidden = !multiple;
                codeMenu.className = multiple ? 'dropdown-menu w-100 p-2 shadow' : '';
                codeMenu.style.zIndex = multiple ? '1050' : '';
                codeMenu.hidden = multiple;
                codeToggle.setAttribute('aria-expanded', 'false');
                codeLabel.htmlFor = multiple ? 'inventoryCodeToggle' : 'inventoryCode0';
                updateCodeSummary();
            };
            quantity.addEventListener('input', syncCodes);
            syncCodes();
            function setupPicker(prefix, addId, attribute) {
            const picker = document.getElementById(prefix + 'Picker');
            const input = document.getElementById(prefix);
            const menu = document.getElementById(prefix + 'Menu');
            const search = document.getElementById(prefix + 'Search');
            const add = document.getElementById(addId);
            const hint = document.getElementById(prefix + 'Hint');
            const empty = document.getElementById(prefix + 'Empty');
            const options = [...picker.querySelectorAll('[' + attribute + ']')];
            const close = () => {
                menu.hidden = true;
                menu.classList.remove('show');
                input.setAttribute('aria-expanded', 'false');
            };
            const filter = () => {
                const name = search.value.trim();
                const term = name.toLocaleLowerCase('id');
                let matches = 0;
                options.forEach(option => {
                    option.hidden = !option.getAttribute(attribute).toLocaleLowerCase('id').includes(term);
                    if (!option.hidden) matches++;
                });
                const exists = options.some(option => option.getAttribute(attribute).toLocaleLowerCase('id') === term);
                add.hidden = !name || exists;
                add.textContent = '+ Tambahkan "' + name + '"';
                hint.hidden = !!name;
                empty.hidden = matches > 0;
            };
            const open = (focusSearch = false) => {
                search.value = input.value;
                filter();
                menu.hidden = false;
                menu.classList.add('show');
                input.setAttribute('aria-expanded', 'true');
                if (focusSearch) search.focus();
            };
            const select = name => {
                input.value = name;
                input.dispatchEvent(new Event('change', { bubbles: true }));
                close();
                input.focus();
            };
            input.addEventListener('click', () => open(true));
            input.addEventListener('input', () => open());
            input.addEventListener('keydown', event => {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    open(true);
                }
            });
            search.addEventListener('input', filter);
            search.addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    const first = options.find(option => !option.hidden);
                    if (!add.hidden) select(search.value.trim());
                    else if (first) select(first.getAttribute(attribute));
                }
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    const first = !add.hidden ? add : options.find(option => !option.hidden);
                    first?.focus();
                }
            });
            add.addEventListener('click', () => select(search.value.trim()));
            options.forEach(option => option.addEventListener('click', () => select(option.getAttribute(attribute))));
            picker.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    close();
                    input.focus();
                }
            });
            picker.addEventListener('focusout', event => {
                if (!picker.contains(event.relatedTarget)) close();
            });
            document.addEventListener('click', event => {
                if (!picker.contains(event.target)) close();
            });
            }
            setupPicker('itemName', 'addItemName', 'data-item-name');
            setupPicker('itemBrand', 'addItemBrand', 'data-item-brand');
        });
    </script>
@endsection
