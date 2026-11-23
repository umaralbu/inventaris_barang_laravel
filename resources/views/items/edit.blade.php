@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<style>
    .autocomplete-wrapper {
        position: relative;
    }
    .autocomplete-menu {
        position: absolute;
        z-index: 1060;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        max-height: 220px;
        overflow-y: auto;
        padding: 6px;
        border: 1px solid var(--border, #dee2e6);
        border-radius: 8px;
        background: var(--card-bg, #ffffff);
        color: var(--text, #2d2d2d);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    body.dark-mode .autocomplete-menu {
        background: #1e293b;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.12);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    }
    .autocomplete-item {
        display: block;
        width: 100%;
        padding: 8px 12px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: inherit;
        text-align: left;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .autocomplete-item:hover,
    .autocomplete-item.active,
    .autocomplete-item:focus {
        background: var(--primary-soft, #f0ece5);
        color: var(--primary, #8f795d);
        outline: none;
    }
    body.dark-mode .autocomplete-item:hover,
    body.dark-mode .autocomplete-item.active,
    body.dark-mode .autocomplete-item:focus {
        background: rgba(212, 185, 141, 0.18);
        color: #e2cb9f;
    }
    .autocomplete-highlight {
        font-weight: 700;
        text-decoration: underline;
    }
    .current-photo-card {
        border: 1px solid var(--border, #dee2e6);
        border-radius: 8px;
        padding: 8px;
        background: var(--surface-soft, rgba(245, 243, 238, 0.6));
    }
    .preview-thumbnail {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--border, #dee2e6);
    }
    .btn-brand-primary {
        background-color: #2F3185 !important;
        border-color: #2F3185 !important;
        color: #ffffff !important;
        font-weight: 500;
        box-shadow: none !important;
    }
    .btn-brand-primary:hover,
    .btn-brand-primary:focus,
    .btn-brand-primary:active {
        background-color: #24266b !important;
        border-color: #24266b !important;
        color: #ffffff !important;
    }
</style>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Barang</h4>
        <span class="badge bg-secondary-subtle text-secondary px-3 py-2">Kode: {{ $item->code }}</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if($item->status === 'Hilang')
        <div class="alert alert-warning mb-3">
            Barang ini tercatat hilang. Jika ditemukan, periksa kondisi fisiknya lalu sesuaikan kondisi dan statusnya di formulir ini.
        </div>
    @endif

    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="itemCode">Kode Inventaris</label>
                <input type="text" id="itemCode" name="code" class="form-control" value="{{ old('code', $item->code) }}">
            </div>

            <!-- Nama Barang dengan Autocomplete & Scroll Down -->
            <div class="col-md-6">
                <label class="form-label" for="itemNameInput">Nama Barang <span class="text-danger">*</span></label>
                <div class="autocomplete-wrapper" id="nameAutocompleteWrapper">
                    <input type="text" 
                           id="itemNameInput" 
                           name="name" 
                           class="form-control" 
                           value="{{ old('name', $item->name) }}" 
                           placeholder="Ketik atau pilih nama barang..." 
                           autocomplete="off" 
                           required>
                    <div id="nameMenu" class="autocomplete-menu" hidden>
                        <div id="nameOptionsList"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="itemCategory">Kategori</label>
                <div class="autocomplete-wrapper">
                    <input type="text"
                           id="itemCategory"
                           class="form-control"
                           value="{{ $categories->firstWhere('id', old('category_id', $item->category_id))?->name ?? '' }}"
                           placeholder="Ketik atau pilih kategori"
                           autocomplete="off"
                           role="combobox"
                           aria-expanded="false"
                           aria-controls="itemCategoryMenu">
                    <div id="itemCategoryMenu" class="autocomplete-menu" hidden>
                    @foreach($categories as $category)
                        <button type="button" class="autocomplete-item text-wrap" data-value="{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                        <p class="small text-secondary mb-0 p-1" data-empty-message hidden>Tidak ada kategori yang cocok.</p>
                    </div>
                </div>
                <input type="hidden" id="itemCategoryValue" name="category_id" value="{{ old('category_id', $item->category_id) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label" for="itemLocation">Lokasi</label>
                <div class="autocomplete-wrapper">
                    <input type="text"
                           id="itemLocation"
                           class="form-control"
                           value="{{ $locations->firstWhere('id', old('location_id', $item->location_id))?->name ?? '' }}"
                           placeholder="Ketik atau pilih lokasi"
                           autocomplete="off"
                           role="combobox"
                           aria-expanded="false"
                           aria-controls="itemLocationMenu">
                    <div id="itemLocationMenu" class="autocomplete-menu" hidden>
                    @foreach($locations as $location)
                        <button type="button" class="autocomplete-item text-wrap" data-value="{{ $location->id }}">{{ $location->name }}</button>
                    @endforeach
                        <p class="small text-secondary mb-0 p-1" data-empty-message hidden>Tidak ada lokasi yang cocok.</p>
                    </div>
                </div>
                <input type="hidden" id="itemLocationValue" name="location_id" value="{{ old('location_id', $item->location_id) }}">
            </div>

            <!-- Merek dengan Autocomplete & Scroll Down -->
            <div class="col-md-4">
                <label class="form-label" for="itemBrandInput">Merek</label>
                <div class="autocomplete-wrapper" id="brandAutocompleteWrapper">
                    <input type="text" 
                           id="itemBrandInput" 
                           name="brand" 
                           class="form-control" 
                           value="{{ old('brand', $item->brand) }}" 
                           placeholder="Ketik atau pilih merek..." 
                           autocomplete="off">
                    <div id="brandMenu" class="autocomplete-menu" hidden>
                        <div id="brandOptionsList"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="itemSpecification">Spesifikasi</label>
                <textarea id="itemSpecification" name="specification" class="form-control" rows="3" placeholder="Contoh: Intel i5, RAM 8GB, SSD 512GB">{{ old('specification', $item->specification) }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="itemDescription">Keterangan</label>
                <textarea id="itemDescription" name="description" class="form-control" rows="3" placeholder="Keterangan tambahan barang...">{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="col-md-2">
                <label class="form-label" for="itemQuantity">Jumlah</label>
                <input type="number" id="itemQuantity" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity) }}" min="0">
            </div>

            <div class="col-md-2">
                <label class="form-label" for="itemUnit">Satuan</label>
                <input type="text" id="itemUnit" name="unit" class="form-control" value="{{ old('unit', $item->unit) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label" for="itemCondition">Kondisi</label>
                <select id="itemCondition" name="condition" class="form-select">
                    @foreach(['Baik', 'Rusak', 'Perlu Perbaikan'] as $cond)
                        <option value="{{ $cond }}" {{ old('condition', $item->condition) == $cond ? 'selected' : '' }}>{{ $cond }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="itemStatus">Status</label>
                <select id="itemStatus" name="status" class="form-select">
                    @foreach(['Tersedia', 'Dipinjam', 'Hilang'] as $status)
                        <option value="{{ $status }}" {{ old('status', $item->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="itemPurchaseDate">Tanggal Pembelian</label>
                <input type="date" id="itemPurchaseDate" name="purchase_date" class="form-control" value="{{ old('purchase_date', optional($item->purchase_date)->format('Y-m-d')) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="purchasePriceDisplay">Harga Pembelian</label>
                <div class="input-group">
                    <span class="input-group-text bg-light fw-semibold text-secondary">Rp</span>
                    <input type="text" 
                           id="purchasePriceDisplay" 
                           class="form-control" 
                           placeholder="0" 
                           autocomplete="off" 
                           inputmode="numeric"
                           value="{{ old('purchase_price') !== null ? number_format((float)old('purchase_price'), 0, ',', '.') : ($item->purchase_price ? number_format((float)$item->purchase_price, 0, ',', '.') : '0') }}">
                </div>
                <input type="hidden" 
                       id="itemPurchasePrice" 
                       name="purchase_price" 
                       value="{{ old('purchase_price', (int)$item->purchase_price) }}">
                <small class="form-text text-muted">Harga harus diisi.</small>
            </div>

            <!-- Fitur Foto Barang: 1 Tombol dengan Pop-up Modal -->
            <div class="col-md-6">
                <label class="form-label d-block">Foto Barang</label>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-brand-primary" data-bs-toggle="modal" data-bs-target="#photoTermsModal">
                        {{ $item->photo ? 'Ubah Foto Barang' : 'Upload Foto Barang' }}
                    </button>
                    <span id="photoStatusBadge">
                        @if($item->photo)
                            <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-2">
                                Sudah ada foto
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary py-2 px-2">Belum ada foto</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-brand-primary px-4">
                Simpan Perubahan
            </button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <!-- Modal Pop-up Ketentuan Upload Foto & Tombol Choose File (Minimalis) -->
        <div class="modal fade" id="photoTermsModal" tabindex="-1" aria-labelledby="photoTermsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-2 px-3" style="background-color: #2F3185; color: #ffffff;">
                        <h6 class="modal-title mb-0 fw-semibold text-white" id="photoTermsModalLabel">
                            Upload Foto Barang
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body py-3 px-3">
                        <!-- Ketentuan / Syarat Foto -->
                        <div class="p-3 rounded mb-3 small text-secondary bg-light border">
                            <div class="fw-semibold text-dark mb-1">Syarat &amp; Ketentuan Foto:</div>
                            <ul class="mb-0 ps-3">
                                <li>Format yang diterima: <strong>.jpg, .jpeg, .png</strong></li>
                                <li>Ukuran file: <strong>Maksimal 2 MB</strong></li>
                                <li>Gunakan foto asli fisik barang yang jelas &amp; fokus</li>
                            </ul>
                        </div>

                        <!-- Tombol Choose File -->
                        <div class="mb-3">
                            <label for="itemPhotoInput" class="form-label small fw-semibold">Pilih Berkas Foto:</label>
                            <input type="file" 
                                   id="itemPhotoInput" 
                                   name="photo" 
                                   class="form-control" 
                                   accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                   onchange="previewPhoto(event)">
                        </div>

                        <!-- Pratinjau Foto Saat Ini & Foto Baru -->
                        <div class="d-flex flex-column gap-2">
                            @if($item->photo)
                                <div class="current-photo-card d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $item->photo) }}" 
                                         alt="Foto Saat Ini" 
                                         class="preview-thumbnail"
                                         onerror="this.onerror=null; this.src=''; this.parentElement.innerHTML='<span class=\'badge bg-warning text-dark\'>Foto belum dapat dimuat</span>';">
                                    <div>
                                        <div class="small fw-semibold">Foto Saat Ini</div>
                                        <small class="text-muted">Akan digantikan bila memilih berkas baru di atas</small>
                                    </div>
                                </div>
                            @endif

                            <div id="newPhotoPreviewCard" class="current-photo-card d-none align-items-center gap-3">
                                <img id="newPhotoPreviewImg" src="" alt="Pratinjau Foto Baru" class="preview-thumbnail">
                                <div>
                                    <div class="small fw-semibold text-success">Pratinjau File Baru</div>
                                    <small id="newPhotoInfo" class="text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 border-0">
                        <button type="button" class="btn btn-sm btn-brand-primary px-3" data-bs-dismiss="modal">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. Data Rekomendasi
    const availableItemNames = @json($itemNames ?? []);
    const availableItemBrands = @json($itemBrands ?? []);
    const brandsByItemName = @json($brandsByItemName ?? []);

    function setupSearchableReference(inputId, hiddenId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const menu = document.getElementById(`${inputId}Menu`);
        if (!input || !hidden || !menu) return;

        const options = Array.from(menu.querySelectorAll('.autocomplete-item'));
        const emptyMessage = menu.querySelector('[data-empty-message]');
        const wrapper = input.closest('.autocomplete-wrapper');

        const render = () => {
            const query = input.value.trim().toLocaleLowerCase('id');
            let visibleCount = 0;
            options.forEach((option) => {
                const matches = option.textContent.trim().toLocaleLowerCase('id').includes(query);
                option.hidden = !matches;
                if (matches) visibleCount++;
            });
            if (emptyMessage) emptyMessage.hidden = visibleCount > 0;
            menu.hidden = false;
            input.setAttribute('aria-expanded', 'true');
        };

        input.addEventListener('focus', render);
        input.addEventListener('input', () => {
            hidden.value = '';
            render();
        });
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                menu.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            } else if (event.key === 'Enter' && !menu.hidden) {
                const firstOption = options.find((option) => !option.hidden);
                if (firstOption) {
                    event.preventDefault();
                    firstOption.click();
                }
            }
        });
        options.forEach((option) => {
            option.addEventListener('click', () => {
                input.value = option.textContent.trim();
                hidden.value = option.dataset.value;
                menu.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            });
        });
        document.addEventListener('click', (event) => {
            if (!wrapper.contains(event.target)) {
                menu.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /**
     * Mengambil daftar merek dinamis HANYA sesuai nama barang yang sedang diinput
     */
    function getDynamicBrandList() {
        const nameInput = document.getElementById('itemNameInput');
        const currentName = (nameInput?.value || '').trim();

        let matchingBrands = [];
        if (currentName) {
            if (brandsByItemName[currentName] && Array.isArray(brandsByItemName[currentName])) {
                matchingBrands = [...brandsByItemName[currentName]];
            } else {
                const foundKey = Object.keys(brandsByItemName).find(k => k.toLowerCase() === currentName.toLowerCase());
                if (foundKey && Array.isArray(brandsByItemName[foundKey])) {
                    matchingBrands = [...brandsByItemName[foundKey]];
                }
            }
        }

        // HANYA tampilkan merek dari nama barang tersebut (tidak menampilkan merek dari barang lain)
        return Array.from(new Set(matchingBrands));
    }

    /**
     * Setup Reusable Autocomplete with Scroll Down & Top-Ranked Matching
     */
    function setupAutocomplete(inputId, menuId, listId, dataProvider) {
        const input = document.getElementById(inputId);
        const menu = document.getElementById(menuId);
        const list = document.getElementById(listId);

        if (!input || !menu || !list) return;

        let activeIndex = -1;

        function getDataList() {
            return typeof dataProvider === 'function' ? dataProvider() : dataProvider;
        }

        function getItems() {
            return Array.from(list.querySelectorAll('.autocomplete-item'));
        }

        function updateActiveItem() {
            const items = getItems();
            items.forEach((btn, idx) => {
                if (idx === activeIndex) {
                    btn.classList.add('active');
                    btn.scrollIntoView({ block: 'nearest' });
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function renderOptions(query) {
            const trimmed = (query || '').trim().toLowerCase();
            const dataList = getDataList();
            list.innerHTML = '';
            activeIndex = -1;

            let filtered = [];

            if (!trimmed) {
                // Tampilkan semua opsi yang tersedia (bisa scroll down)
                filtered = [...dataList];
            } else {
                // Pisahkan: prefix match (paling atas) vs substring match
                const prefixMatches = [];
                const containsMatches = [];

                dataList.forEach(item => {
                    const lower = item.toLowerCase();
                    if (lower.startsWith(trimmed)) {
                        prefixMatches.push(item);
                    } else if (lower.includes(trimmed)) {
                        containsMatches.push(item);
                    }
                });

                // Rekomendasi yang paling cocok diletakkan di PALING ATAS
                filtered = [...prefixMatches, ...containsMatches];
            }

            if (filtered.length === 0) {
                const emptyNotice = document.createElement('div');
                emptyNotice.className = 'small text-muted py-2 px-3 text-center';
                emptyNotice.textContent = 'Tidak ada merek lain untuk barang ini. Anda dapat mengetik merek baru.';
                list.appendChild(emptyNotice);
            } else {
                filtered.forEach(item => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'autocomplete-item';

                    if (trimmed) {
                        const regex = new RegExp(`(${trimmed.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                        btn.innerHTML = item.replace(regex, '<span class="autocomplete-highlight">$1</span>');
                    } else {
                        btn.textContent = item;
                    }

                    btn.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        input.value = item;
                        closeMenu();
                    });

                    list.appendChild(btn);
                });
            }

            menu.hidden = false;
        }

        function closeMenu() {
            menu.hidden = true;
            activeIndex = -1;
        }

        input.addEventListener('focus', () => {
            renderOptions(input.value);
        });

        input.addEventListener('input', () => {
            renderOptions(input.value);
        });

        // Tutup jika klik di luar
        document.addEventListener('click', (event) => {
            if (!input.contains(event.target) && !menu.contains(event.target)) {
                closeMenu();
            }
        });

        // Navigasi keyboard (Arrow Down, Arrow Up, Enter, Escape)
        input.addEventListener('keydown', (e) => {
            const items = getItems();
            if (menu.hidden || items.length === 0) {
                if (e.key === 'ArrowDown' && document.activeElement === input) {
                    renderOptions(input.value);
                    e.preventDefault();
                }
                return;
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveItem();
            } else if (e.key === 'Enter') {
                if (activeIndex >= 0 && items[activeIndex]) {
                    e.preventDefault();
                    items[activeIndex].click();
                }
            } else if (e.key === 'Escape') {
                closeMenu();
            }
        });
    }

    // Inisialisasi Autocomplete untuk Nama Barang dan Merek
    setupSearchableReference('itemCategory', 'itemCategoryValue');
    setupSearchableReference('itemLocation', 'itemLocationValue');
    setupAutocomplete('itemNameInput', 'nameMenu', 'nameOptionsList', availableItemNames);
    setupAutocomplete('itemBrandInput', 'brandMenu', 'brandOptionsList', getDynamicBrandList);

    // 2. Pratinjau Foto Baru Saat Dipilih
    function previewPhoto(event) {
        const input = event.target;
        const previewCard = document.getElementById('newPhotoPreviewCard');
        const previewImg = document.getElementById('newPhotoPreviewImg');
        const previewInfo = document.getElementById('newPhotoInfo');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validasi client-side ekstensi sederhana
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                alert('Format file tidak didukung! Mohon pilih file JPG atau PNG.');
                input.value = '';
                previewCard.classList.add('d-none');
                previewCard.classList.remove('d-flex');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewInfo.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                previewCard.classList.remove('d-none');
                previewCard.classList.add('d-flex');

                const statusBadge = document.getElementById('photoStatusBadge');
                if (statusBadge) {
                    statusBadge.innerHTML = `<span class="badge bg-primary text-white py-2 px-2"><i class="bi bi-file-earmark-check me-1"></i>${file.name}</span>`;
                }
            };
            reader.readAsDataURL(file);
        } else {
            previewCard.classList.add('d-none');
            previewCard.classList.remove('d-flex');
        }
    }

    // 3. Format Otomatis Input Harga Pembelian (Rupiah)
    const priceDisplay = document.getElementById('purchasePriceDisplay');
    const priceHidden = document.getElementById('itemPurchasePrice');

    if (priceDisplay && priceHidden) {
        function formatRupiahNumber(val) {
            const rawDigits = String(val).replace(/[^0-9]/g, '');
            if (!rawDigits) {
                return { formatted: '0', raw: 0 };
            }
            const intVal = parseInt(rawDigits, 10);
            return {
                formatted: new Intl.NumberFormat('id-ID').format(intVal),
                raw: intVal
            };
        }

        priceDisplay.addEventListener('input', function () {
            const { formatted, raw } = formatRupiahNumber(this.value);
            this.value = formatted;
            priceHidden.value = raw;
        });

        priceDisplay.addEventListener('focus', function () {
            if (this.value === '0') {
                this.value = '';
            }
        });

        priceDisplay.addEventListener('blur', function () {
            if (this.value.trim() === '') {
                this.value = '0';
                priceHidden.value = 0;
            }
        });
    }
</script>
@endsection
