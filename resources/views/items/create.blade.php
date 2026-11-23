@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<style>
    .form-page-container {
        width: 100%;
        padding-bottom: 40px;
    }
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    body.dark-mode .form-card {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.08);
    }
    .step-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .step-badge-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #2F3185;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .step-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    body.dark-mode .step-title {
        color: #f1f5f9;
    }
    .status-pill {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
    }
    body.dark-mode .status-pill {
        background: #334155;
        color: #cbd5e1;
    }
    .form-label-custom {
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    body.dark-mode .form-label-custom {
        color: #cbd5e1;
    }
    .form-control-custom,
    .form-select-custom {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.92rem;
        color: #1e293b;
        background-color: #ffffff;
        width: 100%;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control-custom:focus,
    .form-select-custom:focus {
        border-color: #2F3185;
        outline: none;
        box-shadow: 0 0 0 3px rgba(47, 49, 133, 0.12);
    }
    body.dark-mode .form-control-custom,
    body.dark-mode .form-select-custom {
        background-color: #0f172a;
        border-color: #334155;
        color: #f8fafc;
    }
    .price-input-group {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: stretch;
        width: 100%;
    }
    .price-input-group .price-prefix {
        display: inline-flex;
        align-items: center;
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        font-size: 0.92rem;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-right: none;
        border-radius: 8px 0 0 8px;
        white-space: nowrap;
    }
    body.dark-mode .price-input-group .price-prefix {
        background-color: #1e293b;
        color: #94a3b8;
        border-color: #334155;
    }
    .price-input-group .price-input {
        border-radius: 0 8px 8px 0 !important;
        flex: 1 1 auto;
        min-width: 0;
        width: auto !important;
    }
    .helper-text-custom {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 5px;
        display: block;
    }
    body.dark-mode .helper-text-custom {
        color: #94a3b8;
    }
    .photo-dropzone {
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        padding: 24px 16px;
        text-align: center;
        background-color: #fafbfc;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .photo-dropzone:hover {
        background-color: #f1f5f9;
        border-color: #2F3185;
    }
    body.dark-mode .photo-dropzone {
        background-color: #0f172a;
        border-color: #334155;
    }
    .btn-brand-primary {
        background-color: #2F3185 !important;
        border-color: #2F3185 !important;
        color: #ffffff !important;
        font-weight: 600;
        border-radius: 8px;
        padding: 9px 20px;
    }
    .btn-brand-primary:hover {
        background-color: #24266b !important;
        border-color: #24266b !important;
    }
    .btn-cancel-custom {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-weight: 600;
        border-radius: 8px;
        padding: 9px 20px;
    }
    .btn-cancel-custom:hover {
        background-color: #f8fafc;
        color: #0f172a;
    }
    body.dark-mode .btn-cancel-custom {
        background-color: #1e293b;
        border-color: #475569;
        color: #f1f5f9;
    }
    .brand-photo-card {
        border: 1px solid #1CBDB3;
        background: rgba(28, 189, 179, 0.08);
        border-radius: 8px;
    }
    body.dark-mode .brand-photo-card {
        background: rgba(28, 189, 179, 0.15);
        border-color: #1CBDB3;
    }
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
</style>

@php
    $initialQuantity = (int) old('quantity', 1);
    $initialCodes = old('codes');
    if (!is_array($initialCodes) || empty($initialCodes)) {
        $initialCodes = App\Models\Item::generateCodes(max(1, $initialQuantity));
    } else {
        $seen = [];
        $fixedCodes = [];
        foreach ($initialCodes as $c) {
            $clower = mb_strtolower(trim((string)$c));
            if ($clower === '' || in_array($clower, $seen, true)) {
                $replacement = App\Models\Item::generateCodes(1, array_merge($seen, $fixedCodes))[0];
                $fixedCodes[] = $replacement;
                $seen[] = mb_strtolower($replacement);
            } else {
                $fixedCodes[] = (string)$c;
                $seen[] = $clower;
            }
        }
        $initialCodes = $fixedCodes;
    }
    $startCode = $initialCodes[0] ?? App\Models\Item::generateCode();
@endphp

<div class="form-page-container">
    <!-- Top Back Link -->
    <div class="mb-3">
        <a href="{{ route('items.index') }}" class="text-decoration-none fw-semibold small" style="color: #2F3185;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Barang
        </a>
    </div>

    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1" style="color: #1e293b; font-size: 1.75rem;">Tambah Barang Baru</h2>
        <p class="text-secondary small mb-0">Hanya 3 kolom bertanda * yang wajib diisi. Sisanya bisa dilengkapi nanti.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="fw-semibold mb-1">Terjadi kesalahan pada input:</div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Nilai default tersembunyi untuk satuan, kondisi, status -->
        <input type="hidden" name="unit" value="{{ old('unit', 'pcs') }}">
        <input type="hidden" name="condition" value="{{ old('condition', 'Baik') }}">
        <input type="hidden" name="status" value="{{ old('status', 'Tersedia') }}">

        <!-- Container Hidden Codes untuk Pengiriman Form -->
        <div id="hiddenCodesContainer">
            @foreach($initialCodes as $code)
                <input type="hidden" name="codes[]" value="{{ $code }}">
            @endforeach
        </div>

        <!-- 1. INFORMASI UTAMA -->
        <div class="form-card">
            <div class="step-header">
                <div class="step-badge-group">
                    <span class="step-number">1</span>
                    <h5 class="step-title">Informasi utama</h5>
                </div>
                <span class="status-pill">Wajib</span>
            </div>

            <div class="row g-3">
                <!-- Baris 1: Nama Barang & Jumlah Unit -->
                <div class="col-md-8">
                    <label class="form-label-custom" for="itemName">
                        Nama barang <span class="text-danger">*</span>
                    </label>
                    <div class="autocomplete-wrapper" id="itemNamePicker">
                        <input type="text" 
                               id="itemName" 
                               name="name" 
                               class="form-control-custom" 
                               value="{{ old('name') }}" 
                               placeholder="Kalkulator" 
                               autocomplete="off" 
                               maxlength="255" 
                               aria-expanded="false" 
                               aria-controls="itemNameMenu" 
                               required>
                        <div id="itemNameMenu" class="autocomplete-menu" hidden>
                            <input type="search" id="itemNameSearch" class="form-control form-control-sm mb-2" placeholder="Cari nama barang..." autocomplete="off">
                            <button type="button" id="addItemName" class="btn btn-sm btn-outline-primary w-100 text-start mb-2" hidden></button>
                            <div id="itemNameOptions" style="max-height: 180px; overflow-y: auto;">
                                @foreach($itemNames as $itemName)
                                    <button type="button" class="autocomplete-item text-wrap" data-item-name="{{ $itemName }}">{{ $itemName }}</button>
                                @endforeach
                            </div>
                            <p id="itemNameEmpty" class="small text-secondary mb-0 p-1" hidden>Tidak ada nama barang yang cocok.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label-custom" for="itemQuantity">
                        Jumlah unit <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           id="itemQuantity" 
                           name="quantity" 
                           class="form-control-custom" 
                           value="{{ old('quantity', count($initialCodes) > 0 ? count($initialCodes) : 1) }}" 
                           placeholder="Contoh: 5" 
                           min="1" 
                           max="1000" 
                           required>
                    <span class="helper-text-custom" id="quantityHelperText">
                        Kode dibuat otomatis untuk tiap unit (INV-0001, INV-0002, dst.).
                    </span>
                </div>

                <!-- Baris 2: Kode Inventaris di bawah Nama Barang (Bisa Otomatis & Bisa Dicustom) -->
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label-custom mb-0" for="startInventoryCode">
                            Kode inventaris
                        </label>
                        <button type="button" id="btnResetAutoCode" class="btn btn-sm btn-link text-decoration-none p-0 text-primary small" style="font-size: 0.8rem;">
                            <i class="bi bi-arrow-clockwise me-1"></i>Generate Otomatis
                        </button>
                    </div>
                    <input type="text" 
                           id="startInventoryCode" 
                           class="form-control-custom" 
                           value="{{ $startCode }}" 
                           placeholder="Contoh: INV-0001 atau ketik kode custom sendiri...">
                    <span class="helper-text-custom" id="codeHelperText">
                        Kode otomatis dari sistem, atau ketik kode custom Anda sendiri.
                    </span>

                    <!-- Rincian kode per unit jika jumlah unit > 1 -->
                    <div id="unitCodesPreviewWrap" class="mt-2 p-2 border rounded bg-light-subtle d-none">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-secondary">Rincian Kode per Unit (<span id="unitCountPreviewLabel">2</span> Unit):</span>
                            <small class="text-muted">Bisa diedit manual</small>
                        </div>
                        <div id="unitCodesGrid" class="row g-2" style="max-height: 160px; overflow-y: auto;">
                            <!-- Unit inputs -->
                        </div>
                    </div>
                    <div id="codeDuplicateAlert" class="text-danger small mt-1 d-none">
                        <i class="bi bi-exclamation-triangle me-1"></i> Terdapat kode inventaris yang sama. Setiap unit harus memiliki kode berbeda.
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. DETAIL BARANG -->
        <div class="form-card">
            <div class="step-header">
                <div class="step-badge-group">
                    <span class="step-number">2</span>
                    <h5 class="step-title">Detail barang</h5>
                </div>
                <span class="status-pill">Opsional</span>
            </div>

            <div class="row g-3">
                <!-- Kategori -->
                <div class="col-md-4">
                    <label class="form-label-custom" for="itemCategory">Kategori</label>
                    <div class="autocomplete-wrapper">
                        <input type="text"
                               id="itemCategory"
                               class="form-control-custom"
                               value="{{ old('category_id') ? ($categories->firstWhere('id', old('category_id'))?->name ?? '') : '' }}"
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
                    <input type="hidden" id="itemCategoryValue" name="category_id" value="{{ old('category_id') }}">
                </div>

                <!-- Lokasi -->
                <div class="col-md-4">
                    <label class="form-label-custom" for="itemLocation">Lokasi</label>
                    <div class="autocomplete-wrapper">
                        <input type="text"
                               id="itemLocation"
                               class="form-control-custom"
                               value="{{ old('location_id') ? ($locations->firstWhere('id', old('location_id'))?->name ?? '') : '' }}"
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
                    <input type="hidden" id="itemLocationValue" name="location_id" value="{{ old('location_id') }}">
                </div>

                <!-- Merek -->
                <div class="col-md-4">
                    <label class="form-label-custom" for="itemBrand">Merek</label>
                    <div class="autocomplete-wrapper" id="itemBrandPicker">
                        <input type="text" 
                               id="itemBrand" 
                               name="brand" 
                               class="form-control-custom" 
                               value="{{ old('brand') }}" 
                               placeholder="Ketik atau pilih merek" 
                               autocomplete="off" 
                               maxlength="255" 
                               aria-expanded="false" 
                               aria-controls="itemBrandMenu">
                        <div id="itemBrandMenu" class="autocomplete-menu" hidden>
                            <input type="search" id="itemBrandSearch" class="form-control form-control-sm mb-2" placeholder="Cari merek..." autocomplete="off">
                            <button type="button" id="addItemBrand" class="btn btn-sm btn-outline-primary w-100 text-start mb-2" hidden></button>
                            <div id="itemBrandOptions" style="max-height: 180px; overflow-y: auto;">
                                @foreach($itemBrands as $itemBrand)
                                    <button type="button" class="autocomplete-item text-wrap" data-item-brand="{{ $itemBrand }}">{{ $itemBrand }}</button>
                                @endforeach
                            </div>
                            <p id="itemBrandEmpty" class="small text-secondary mb-0 p-1" hidden>Tidak ada merek yang cocok.</p>
                        </div>
                    </div>
                </div>

                <!-- Spesifikasi -->
                <div class="col-md-6">
                    <label class="form-label-custom" for="itemSpecification">Spesifikasi</label>
                    <textarea id="itemSpecification" 
                              name="specification" 
                              class="form-control-custom" 
                              rows="3" 
                              placeholder="Contoh: Prosesor Core i5, RAM 8GB, SSD 512GB">{{ old('specification') }}</textarea>
                </div>

                <!-- Keterangan -->
                <div class="col-md-6">
                    <label class="form-label-custom" for="itemDescription">Keterangan</label>
                    <textarea id="itemDescription" 
                              name="description" 
                              class="form-control-custom" 
                              rows="3" 
                              placeholder="Catatan tambahan, mis. kondisi atau penanggung jawab">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. PEMBELIAN & FOTO -->
        <div class="form-card">
            <div class="step-header">
                <div class="step-badge-group">
                    <span class="step-number">3</span>
                    <h5 class="step-title">Pembelian &amp; foto</h5>
                </div>
                <span class="status-pill">Opsional</span>
            </div>

            <div class="row g-3">
                <!-- Tanggal Pembelian -->
                <div class="col-md-6">
                    <label class="form-label-custom" for="itemPurchaseDate">Tanggal pembelian</label>
                    <input type="date" 
                           id="itemPurchaseDate" 
                           name="purchase_date" 
                           class="form-control-custom" 
                           value="{{ old('purchase_date') }}">
                </div>

                <!-- Harga Pembelian -->
                <div class="col-md-6">
                    <label class="form-label-custom" for="purchasePriceDisplay">Harga pembelian (per unit)</label>
                    <div class="price-input-group">
                        <span class="price-prefix">Rp</span>
                        <input type="text" 
                               id="purchasePriceDisplay" 
                               class="form-control-custom price-input" 
                               placeholder="0" 
                               autocomplete="off" 
                               inputmode="numeric" 
                               value="{{ old('purchase_price') ? number_format((float)old('purchase_price'), 0, ',', '.') : '' }}">
                    </div>
                    <input type="hidden" 
                           id="itemPurchasePrice" 
                           name="purchase_price" 
                           value="{{ old('purchase_price') }}">
                    <small class="helper-text-custom">Harga harus diisi.</small>
                </div>

                <!-- Foto Barang -->
                <div class="col-12">
                    <label class="form-label-custom">Foto barang</label>

                    <!-- Box Preview Foto Otomatis Sesuai Merek -->
                    <div id="brandPhotoBox" class="brand-photo-card p-3 mb-2 d-none">
                        <div class="d-flex align-items-center gap-3">
                            <img id="brandPhotoImg" src="" alt="Foto Merek" class="rounded border shadow-sm" style="width: 56px; height: 56px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge text-white px-2 py-1" style="background-color: #2F3185;">
                                        <i class="bi bi-image me-1"></i> Foto Sesuai Merek
                                    </span>
                                    <span class="fw-bold text-dark small" id="brandPhotoLabel"></span>
                                </div>
                                <p class="small text-muted mb-0">
                                    Merek ini sudah memiliki foto. Foto di atas otomatis digunakan tanpa perlu mengunggah foto baru.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Dropzone Upload Foto -->
                    <div class="photo-dropzone" id="photoDropzone">
                        <i class="bi bi-upload fs-3 text-secondary mb-2"></i>
                        <span class="fw-semibold text-dark small mb-1">Klik untuk memilih foto</span>
                        <span class="text-secondary small" style="font-size: 0.78rem;">Format JPG, JPEG, atau PNG. Maksimal 2 MB.</span>
                        <input type="file" 
                               id="itemPhotoInput" 
                               name="photo" 
                               class="d-none" 
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                    </div>

                    <!-- Preview Jika Pengguna Memilih File Baru -->
                    <div id="newPhotoPreviewCard" class="mt-2 p-2 border rounded bg-white d-none align-items-center gap-3">
                        <img id="newPhotoPreviewImg" src="" alt="Pratinjau Foto Baru" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                        <div class="flex-grow-1">
                            <span class="badge bg-success text-white mb-1" style="font-size: 0.75rem;">Foto Baru Dipilih</span>
                            <div id="newPhotoName" class="small text-truncate text-secondary"></div>
                        </div>
                        <button type="button" id="btnCancelNewPhoto" class="btn btn-sm btn-outline-danger" title="Batal ganti foto">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER BUTTONS & KETERANGAN -->
        <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-brand-primary">
                    <i class="bi bi-check2 me-1"></i> Simpan barang
                </button>
                <a href="{{ route('items.index') }}" class="btn btn-cancel-custom">
                    Batal
                </a>
            </div>
            <div>
                <span class="text-danger">*</span> <span class="text-secondary small">wajib diisi</span>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Data dari Backend
        const brandPhotos = @json($brandPhotos ?? []);
        const brandsByItemName = @json($brandsByItemName ?? []);
        const existingDbCodes = new Set(@json($existingCodes ?? []).map(c => String(c).trim().toLowerCase()));
        
        // Elemen Form
        const quantityInput = document.getElementById('itemQuantity');
        const startCodeInput = document.getElementById('startInventoryCode');
        const hiddenCodesContainer = document.getElementById('hiddenCodesContainer');
        const unitCodesPreviewWrap = document.getElementById('unitCodesPreviewWrap');
        const unitCodesGrid = document.getElementById('unitCodesGrid');
        const unitCountPreviewLabel = document.getElementById('unitCountPreviewLabel');
        const codeDuplicateAlert = document.getElementById('codeDuplicateAlert');
        const btnResetAutoCode = document.getElementById('btnResetAutoCode');
        const quantityHelperText = document.getElementById('quantityHelperText');
        const itemBrandInput = document.getElementById('itemBrand');
        const itemPhotoInput = document.getElementById('itemPhotoInput');
        const photoDropzone = document.getElementById('photoDropzone');
        const brandPhotoBox = document.getElementById('brandPhotoBox');
        const brandPhotoImg = document.getElementById('brandPhotoImg');
        const brandPhotoLabel = document.getElementById('brandPhotoLabel');
        const newPhotoPreviewCard = document.getElementById('newPhotoPreviewCard');
        const newPhotoPreviewImg = document.getElementById('newPhotoPreviewImg');
        const newPhotoName = document.getElementById('newPhotoName');
        const btnCancelNewPhoto = document.getElementById('btnCancelNewPhoto');
        const priceDisplay = document.getElementById('purchasePriceDisplay');
        const priceHidden = document.getElementById('itemPurchasePrice');

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

        setupSearchableReference('itemCategory', 'itemCategoryValue');
        setupSearchableReference('itemLocation', 'itemLocationValue');

        // 2. Format Harga Pembelian (Rupiah)
        if (priceDisplay && priceHidden) {
            const formatRupiah = (val) => {
                const digits = String(val).replace(/\D/g, '');
                if (!digits) return '';
                return new Intl.NumberFormat('id-ID').format(parseInt(digits, 10));
            };
            priceDisplay.addEventListener('input', () => {
                const raw = priceDisplay.value.replace(/\D/g, '');
                priceHidden.value = raw ? raw : '';
                priceDisplay.value = raw ? formatRupiah(raw) : '';
            });
        }

        // 3. Logika Kode Inventaris (Otomatis & Bisa Dicustom)
        function getNextAutoCode(usedSet, startSeq = 1) {
            let seq = Math.max(1, startSeq);
            while (true) {
                const candidate = 'INV-' + String(seq).padStart(4, '0');
                const lower = candidate.toLowerCase();
                if (!existingDbCodes.has(lower) && !usedSet.has(lower)) {
                    usedSet.add(lower);
                    return { code: candidate, nextSeq: seq + 1 };
                }
                seq++;
            }
        }

        function generateSequenceFromPattern(baseCode, count) {
            const codes = [];
            baseCode = (baseCode || '').trim();
            if (!baseCode) return [];

            const match = baseCode.match(/^(.*?)(\d+)$/);
            if (match) {
                const prefix = match[1];
                const numStr = match[2];
                const numLen = numStr.length;
                const startNum = parseInt(numStr, 10);

                for (let i = 0; i < count; i++) {
                    const currentNum = startNum + i;
                    const padded = String(currentNum).padStart(numLen, '0');
                    codes.push(prefix + padded);
                }
            } else {
                for (let i = 0; i < count; i++) {
                    codes.push(`${baseCode}-${i + 1}`);
                }
            }
            return codes;
        }

        function checkDuplicates(codes) {
            const counts = {};
            codes.forEach(c => {
                const lower = c.trim().toLowerCase();
                if (lower) counts[lower] = (counts[lower] || 0) + 1;
            });
            return Object.values(counts).some(cnt => cnt > 1);
        }

        function syncAllCodes(isReset = false) {
            const qty = Math.max(1, parseInt(quantityInput.value || 1, 10));
            let currentBase = (startCodeInput.value || '').trim();

            if (isReset || !currentBase) {
                const auto = getNextAutoCode(new Set(), 1);
                currentBase = auto.code;
                startCodeInput.value = currentBase;
            }

            // Generate codes berdasarkan pattern baseCode
            const codes = generateSequenceFromPattern(currentBase, qty);

            // Sync ke hidden inputs
            hiddenCodesContainer.innerHTML = '';
            codes.forEach(c => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'codes[]';
                hidden.value = c;
                hiddenCodesContainer.appendChild(hidden);
            });

            // Tampilkan rincian unit jika quantity > 1
            if (qty > 1) {
                unitCodesPreviewWrap.classList.remove('d-none');
                unitCountPreviewLabel.textContent = qty;
                renderUnitGrid(codes);
                if (quantityHelperText) {
                    quantityHelperText.textContent = `Kode dibuat otomatis untuk tiap unit (${codes[0]}, ${codes[1] || ''}, dst.).`;
                }
            } else {
                unitCodesPreviewWrap.classList.add('d-none');
                if (quantityHelperText) {
                    quantityHelperText.textContent = `Kode dibuat otomatis untuk tiap unit (${codes[0] || currentBase}).`;
                }
            }

            if (codeDuplicateAlert) {
                codeDuplicateAlert.classList.toggle('d-none', !checkDuplicates(codes));
            }
        }

        function renderUnitGrid(codes) {
            unitCodesGrid.innerHTML = '';
            codes.forEach((code, index) => {
                const col = document.createElement('div');
                col.className = 'col-sm-6';
                col.innerHTML = `
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white fw-semibold text-secondary" style="min-width: 58px;">
                            Unit ${index + 1}
                        </span>
                        <input type="text" 
                               class="form-control form-control-sm unit-individual-code" 
                               data-index="${index}" 
                               value="${code}">
                    </div>
                `;
                unitCodesGrid.appendChild(col);
            });
        }

        // Listener saat Jumlah Unit diubah
        quantityInput.addEventListener('input', () => syncAllCodes(false));

        // Listener saat Kode Inventaris utama diedit custom
        startCodeInput.addEventListener('input', () => syncAllCodes(false));

        // Listener saat tombol Reset Otomatis ditekan
        if (btnResetAutoCode) {
            btnResetAutoCode.addEventListener('click', () => syncAllCodes(true));
        }

        // Listener saat kode pada unit tertentu diedit manual di dalam grid
        unitCodesGrid.addEventListener('input', (e) => {
            if (e.target.classList.contains('unit-individual-code')) {
                const idx = parseInt(e.target.dataset.index, 10);
                const hiddenInputs = hiddenCodesContainer.querySelectorAll('input[name="codes[]"]');
                if (hiddenInputs[idx]) {
                    hiddenInputs[idx].value = e.target.value.trim();
                }
                const allCodes = Array.from(unitCodesGrid.querySelectorAll('.unit-individual-code')).map(i => i.value.trim());
                if (codeDuplicateAlert) {
                    codeDuplicateAlert.classList.toggle('d-none', !checkDuplicates(allCodes));
                }
            }
        });

        // 4. Click pada Dropzone untuk Buka File Dialog
        if (photoDropzone) {
            photoDropzone.addEventListener('click', () => {
                itemPhotoInput.click();
            });
        }

        // 5. Autocomplete Helper untuk Nama Barang & Merek
        function setupPicker(inputId, menuId, optionsId, searchId, addBtnId, emptyId, attribute) {
            const input = document.getElementById(inputId);
            const menu = document.getElementById(menuId);
            const optionsContainer = document.getElementById(optionsId);
            const search = document.getElementById(searchId);
            const addBtn = document.getElementById(addBtnId);
            const empty = document.getElementById(emptyId);

            if (!input || !menu || !optionsContainer) return;

            const getOptions = () => [...optionsContainer.querySelectorAll('[' + attribute + ']')];

            const filter = () => {
                const term = (search.value || '').trim().toLocaleLowerCase('id');
                let matches = 0;
                getOptions().forEach(opt => {
                    const val = opt.getAttribute(attribute).toLocaleLowerCase('id');
                    const isMatch = val.includes(term);
                    opt.style.display = isMatch ? 'block' : 'none';
                    if (isMatch) matches++;
                });
                const exact = getOptions().some(opt => opt.getAttribute(attribute).toLocaleLowerCase('id') === term);
                if (addBtn) {
                    addBtn.hidden = !term || exact;
                    addBtn.textContent = '+ Gunakan "' + search.value.trim() + '"';
                }
                if (empty) {
                    empty.hidden = matches > 0;
                }
            };

            const open = (focusSearch = false) => {
                if (search) search.value = input.value;
                filter();
                menu.hidden = false;
                input.setAttribute('aria-expanded', 'true');
                if (focusSearch && search) search.focus();
            };

            const close = () => {
                menu.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            };

            const select = (value) => {
                input.value = value;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                close();
                input.focus();
            };

            input.addEventListener('click', () => open(true));
            input.addEventListener('focus', () => open(false));
            input.addEventListener('input', () => {
                if (search) search.value = input.value;
                filter();
            });

            if (search) {
                search.addEventListener('input', filter);
                search.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const visible = getOptions().find(opt => opt.style.display !== 'none');
                        if (addBtn && !addBtn.hidden) {
                            select(search.value.trim());
                        } else if (visible) {
                            select(visible.getAttribute(attribute));
                        }
                    } else if (e.key === 'Escape') {
                        close();
                    }
                });
            }

            if (addBtn) {
                addBtn.addEventListener('click', () => {
                    select(search ? search.value.trim() : input.value.trim());
                });
            }

            optionsContainer.addEventListener('click', (e) => {
                const btn = e.target.closest('[' + attribute + ']');
                if (btn) {
                    select(btn.getAttribute(attribute));
                }
            });

            document.addEventListener('click', (e) => {
                if (!input.closest('.autocomplete-wrapper')?.contains(e.target)) {
                    close();
                }
            });
        }

        setupPicker('itemName', 'itemNameMenu', 'itemNameOptions', 'itemNameSearch', 'addItemName', 'itemNameEmpty', 'data-item-name');

        // Helper & Setup Picker Merek Berdasarkan Nama Barang
        function getBrandsForCurrentItemName() {
            const nameInput = document.getElementById('itemName');
            const currentName = (nameInput?.value || '').trim();
            if (!currentName) return [];

            if (brandsByItemName[currentName] && Array.isArray(brandsByItemName[currentName])) {
                return brandsByItemName[currentName];
            }

            const lower = currentName.toLowerCase();
            for (const [key, list] of Object.entries(brandsByItemName)) {
                if (key.trim().toLowerCase() === lower) {
                    return Array.isArray(list) ? list : Object.values(list);
                }
            }

            return [];
        }

        function setupBrandPicker() {
            const input = document.getElementById('itemBrand');
            const menu = document.getElementById('itemBrandMenu');
            const optionsContainer = document.getElementById('itemBrandOptions');
            const search = document.getElementById('itemBrandSearch');
            const addBtn = document.getElementById('addItemBrand');
            const empty = document.getElementById('itemBrandEmpty');

            if (!input || !menu || !optionsContainer) return;

            const renderBrandList = () => {
                const validBrands = getBrandsForCurrentItemName();
                const term = (search ? search.value : '').trim().toLocaleLowerCase('id');
                optionsContainer.innerHTML = '';

                const filtered = validBrands.filter(b => b.toLocaleLowerCase('id').includes(term));

                filtered.forEach(brand => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'autocomplete-item text-wrap';
                    btn.setAttribute('data-item-brand', brand);
                    btn.textContent = brand;
                    btn.addEventListener('click', () => {
                        select(brand);
                    });
                    optionsContainer.appendChild(btn);
                });

                const exact = validBrands.some(b => b.toLocaleLowerCase('id') === term);
                if (addBtn) {
                    const typed = (search ? search.value : input.value || '').trim();
                    addBtn.hidden = !typed || exact;
                    addBtn.textContent = '+ Gunakan "' + typed + '"';
                }

                if (empty) {
                    const currentName = (document.getElementById('itemName')?.value || '').trim();
                    if (!currentName) {
                        empty.textContent = 'Pilih atau ketik nama barang terlebih dahulu.';
                        empty.hidden = false;
                    } else if (validBrands.length === 0) {
                        empty.textContent = 'Belum ada merek terdaftar untuk nama barang ini.';
                        empty.hidden = false;
                    } else if (filtered.length === 0) {
                        empty.textContent = 'Tidak ada merek yang cocok.';
                        empty.hidden = false;
                    } else {
                        empty.hidden = true;
                    }
                }
            };

            const open = (focusSearch = false) => {
                if (search) search.value = input.value;
                renderBrandList();
                menu.hidden = false;
                input.setAttribute('aria-expanded', 'true');
                if (focusSearch && search) search.focus();
            };

            const close = () => {
                menu.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            };

            const select = (value) => {
                input.value = value;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                close();
                input.focus();
            };

            input.addEventListener('click', () => open(true));
            input.addEventListener('focus', () => open(false));
            input.addEventListener('input', () => {
                if (search) search.value = input.value;
                renderBrandList();
            });

            if (search) {
                search.addEventListener('input', renderBrandList);
                search.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const first = optionsContainer.querySelector('[data-item-brand]');
                        if (addBtn && !addBtn.hidden) {
                            select(search.value.trim());
                        } else if (first) {
                            select(first.getAttribute('data-item-brand'));
                        }
                    } else if (e.key === 'Escape') {
                        close();
                    }
                });
            }

            if (addBtn) {
                addBtn.addEventListener('click', () => {
                    select(search ? search.value.trim() : input.value.trim());
                });
            }

            document.addEventListener('click', (e) => {
                if (!input.closest('.autocomplete-wrapper')?.contains(e.target)) {
                    close();
                }
            });
        }

        setupBrandPicker();

        // Update pratinjau saat nama barang berubah
        const itemNameField = document.getElementById('itemName');
        if (itemNameField) {
            itemNameField.addEventListener('change', () => {
                updateBrandPhotoDisplay();
            });
        }

        // 6. Fitur Otomatis Foto Berdasarkan Merek
        function findBrandPhoto(brandName) {
            if (!brandName) return null;
            const clean = brandName.trim().toLowerCase();
            for (const [key, value] of Object.entries(brandPhotos)) {
                if (key.trim().toLowerCase() === clean) {
                    return { brand: key, ...value };
                }
            }
            return null;
        }

        const updateBrandPhotoDisplay = () => {
            const brandVal = (itemBrandInput.value || '').trim();
            const found = findBrandPhoto(brandVal);
            const hasNewFile = itemPhotoInput.files && itemPhotoInput.files.length > 0;

            if (found && !hasNewFile) {
                brandPhotoBox.classList.remove('d-none');
                brandPhotoImg.src = found.url;
                brandPhotoLabel.textContent = found.brand;
            } else {
                brandPhotoBox.classList.add('d-none');
            }
        };

        itemBrandInput.addEventListener('input', updateBrandPhotoDisplay);
        itemBrandInput.addEventListener('change', updateBrandPhotoDisplay);

        // Preview File Baru
        itemPhotoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    newPhotoPreviewImg.src = event.target.result;
                    newPhotoName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                    newPhotoPreviewCard.classList.remove('d-none');
                    newPhotoPreviewCard.classList.add('d-flex');
                    updateBrandPhotoDisplay();
                };
                reader.readAsDataURL(file);
            } else {
                newPhotoPreviewCard.classList.remove('d-flex');
                newPhotoPreviewCard.classList.add('d-none');
                updateBrandPhotoDisplay();
            }
        });

        // Batalkan File Baru
        btnCancelNewPhoto.addEventListener('click', () => {
            itemPhotoInput.value = '';
            newPhotoPreviewCard.classList.remove('d-flex');
            newPhotoPreviewCard.classList.add('d-none');
            updateBrandPhotoDisplay();
        });

        // Inisialisasi awal
        syncAllCodes(false);
        updateBrandPhotoDisplay();
    });
</script>
@endsection
