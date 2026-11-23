@extends('layouts.app')

@section('title', 'Kembalikan Barang')

@section('content')
    <style>
        .return-location-picker {
            position: relative;
        }
        .return-location-menu {
            position: absolute;
            z-index: 1060;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            max-height: 240px;
            overflow-y: auto;
            padding: 6px;
            border: 1px solid var(--border, #dee2e6);
            border-radius: 8px;
            background: var(--card-bg, #fff);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
        }
        .return-location-search {
            position: sticky;
            top: 0;
            z-index: 1;
            margin-bottom: .35rem;
        }
        .return-location-options {
            max-height: 190px;
            overflow-y: auto;
        }
        .return-location-option {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 8px 12px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: inherit;
            text-align: left;
        }
        .return-location-option:hover,
        .return-location-option[aria-selected="true"] {
            background: var(--primary-soft, #f0ece5);
        }
        .return-location-origin-badge {
            margin-left: auto;
            padding-left: 1.5rem;
            color: rgba(100, 116, 139, .62);
            white-space: nowrap;
        }
        body.dark-mode .return-location-origin-badge {
            color: rgba(203, 213, 225, .55);
        }
    </style>
    @php
        $selectedBorrowTransaction = $activeBorrowTransactions->firstWhere('id', old('borrow_transaction_id'));
        if (!$selectedBorrowTransaction && $item) {
            $selectedBorrowTransaction = $activeBorrowTransactions->firstWhere('item_id', $item->id);
        }
        $selectedReturnLocation = old('to_location') ?: $selectedBorrowTransaction?->from_location;
    @endphp
    <div class="card p-4">
        <h4 class="mb-3">Kembalikan Barang</h4>
        <form action="{{ route('transactions.return') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Barang</label>
                    <select name="borrow_transaction_id" class="form-select" data-searchable-select required>
                        @if($activeBorrowTransactions->isEmpty())
                            <option value="" selected disabled>Tidak ada pinjaman aktif yang dapat dikembalikan</option>
                        @else
                            <option value="">Pilih barang yang dipinjam</option>
                        @endif
                        @foreach($activeBorrowTransactions as $borrowTransaction)
                            <option value="{{ $borrowTransaction->id }}" data-borrower="{{ $borrowTransaction->to_person }}" data-original-location="{{ $borrowTransaction->from_location }}" @selected((string) old('borrow_transaction_id') === (string) $borrowTransaction->id || ($item && $item->id === $borrowTransaction->item_id))>
                                {{ $borrowTransaction->item->code }} - {{ $borrowTransaction->item->name }} - dipinjam oleh {{ $borrowTransaction->to_person }} ({{ $borrowTransaction->quantity }} {{ $borrowTransaction->item->unit }}) - jatuh tempo: {{ $borrowTransaction->formatted_due_date }}
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
                    <select name="from_person" id="returnStaff" class="form-select" data-searchable-select required>
                        <option value="">Pilih staf yang menyerahkan barang</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->name }}" @selected(old('from_person') === $staffMember->name)>{{ $staffMember->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="returnLocationToggle">Lokasi pengembalian</label>
                    <div class="return-location-picker">
                        <select name="to_location" id="returnLocation" class="visually-hidden" required aria-hidden="true" tabindex="-1">
                            <option value="">Pilih lokasi pengembalian</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->name }}" data-location-label="{{ $location->name }}" @selected($selectedReturnLocation === $location->name)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="returnLocationToggle" class="form-select d-flex align-items-center text-start" aria-haspopup="listbox" aria-expanded="false" aria-controls="returnLocationOptions">
                            <span id="returnLocationSelectedLabel">{{ $selectedReturnLocation ?: 'Pilih lokasi pengembalian' }}</span>
                            <span id="returnLocationSelectedOrigin" class="return-location-origin-badge" @if(!$selectedBorrowTransaction || $selectedReturnLocation !== $selectedBorrowTransaction->from_location) hidden @endif>Lokasi awal</span>
                        </button>
                        <div id="returnLocationMenu" class="return-location-menu" hidden>
                            <input type="search" id="returnLocationSearch" class="form-control return-location-search" placeholder="Cari lokasi..." aria-label="Cari lokasi pengembalian" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="returnLocationOptions" autocomplete="off">
                            <div id="returnLocationOptions" class="return-location-options" role="listbox" aria-label="Lokasi pengembalian">
                                @foreach($locations as $location)
                                    <button type="button" class="return-location-option" role="option" data-value="{{ $location->name }}" data-location-label="{{ $location->name }}" aria-selected="{{ $selectedReturnLocation === $location->name ? 'true' : 'false' }}">
                                        <span>{{ $location->name }}</span>
                                        <span class="return-location-origin-badge" data-origin-badge @if($selectedBorrowTransaction?->from_location !== $location->name) hidden @endif>Lokasi awal</span>
                                    </button>
                                @endforeach
                                <div id="returnLocationEmpty" class="searchable-select-empty" role="status" hidden>Tidak ada lokasi yang cocok.</div>
                            </div>
                        </div>
                    </div>
                    @error('to_location')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Pengembalian</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Waktu Transaksi</label>
                    <input type="time" name="transaction_time" class="form-control" value="{{ old('transaction_time', now()->format('H:i:s')) }}" step="1" required>
                    <div class="form-text">Format 24 jam (HH:mm:ss)</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="returnCondition">Kondisi setelah dikembalikan</label>
                    <select name="condition" id="returnCondition" class="form-select" data-searchable-select data-search-placeholder="">
                        <option @selected(old('condition', 'Baik') === 'Baik')>Baik</option>
                        <option @selected(old('condition') === 'Rusak')>Rusak</option>
                        <option @selected(old('condition') === 'Perlu Perbaikan')>Perlu Perbaikan</option>
                    </select>
                    <div class="form-text">Jika kondisi rusak, lokasi otomatis diatur ke Gudang.</div>
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
            const locationSelect = document.getElementById('returnLocation');
            const locationToggle = document.getElementById('returnLocationToggle');
            const locationMenu = document.getElementById('returnLocationMenu');
            const locationSearch = document.getElementById('returnLocationSearch');
            const locationEmpty = document.getElementById('returnLocationEmpty');
            const locationLabel = document.getElementById('returnLocationSelectedLabel');
            const selectedOriginBadge = document.getElementById('returnLocationSelectedOrigin');
            const conditionSelect = document.getElementById('returnCondition');
            const locationOptions = locationMenu ? Array.from(locationMenu.querySelectorAll('[data-value]')) : [];
            let locationBeforeDamage = locationSelect?.value || '';
            let forceWarehouse = false;

            if (!borrowSelect || !staffSelect) return;

            const selectBorrower = function () {
                const borrower = borrowSelect.selectedOptions[0]?.dataset.borrower;
                if (borrower && Array.from(staffSelect.options).some((option) => option.value === borrower)) {
                    staffSelect.value = borrower;
                    staffSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            };

            const filterLocationOptions = function () {
                const query = locationSearch.value.trim().toLocaleLowerCase('id');
                const isDamaged = conditionSelect?.value === 'Rusak';
                let visibleCount = 0;
                locationOptions.forEach((option) => {
                    option.hidden = (!isDamaged && option.dataset.value === 'Gudang')
                        || !option.textContent.toLocaleLowerCase('id').includes(query);
                    if (!option.hidden) visibleCount++;
                });
                locationEmpty.hidden = visibleCount > 0;
            };

            const updateLocationPicker = function (selectOriginalLocation = false) {
                if (!locationSelect) return;
                const originalLocation = borrowSelect.selectedOptions[0]?.dataset.originalLocation;
                if (selectOriginalLocation && originalLocation && Array.from(locationSelect.options).some((option) => option.value === originalLocation)) {
                    locationSelect.value = originalLocation;
                }
                const selectedOption = locationSelect.selectedOptions[0];
                if (locationLabel) locationLabel.textContent = selectedOption?.dataset.locationLabel || 'Pilih lokasi pengembalian';
                if (selectedOriginBadge) selectedOriginBadge.hidden = !originalLocation || selectedOption?.value !== originalLocation;
                locationOptions.forEach((option) => {
                    option.setAttribute('aria-selected', option.dataset.value === locationSelect.value ? 'true' : 'false');
                    const badge = option.querySelector('[data-origin-badge]');
                    if (badge) badge.hidden = !originalLocation || option.dataset.value !== originalLocation;
                });
            };

            const syncLocationWithCondition = function () {
                if (!conditionSelect || !locationSelect) return;
                const isDamaged = conditionSelect.value === 'Rusak';
                if (isDamaged) {
                    if (!forceWarehouse) locationBeforeDamage = locationSelect.value;
                    forceWarehouse = true;
                    if (Array.from(locationSelect.options).some((option) => option.value === 'Gudang')) {
                        locationSelect.value = 'Gudang';
                    }
                    if (locationToggle) locationToggle.disabled = true;
                } else {
                    if (forceWarehouse && Array.from(locationSelect.options).some((option) => option.value === locationBeforeDamage)) {
                        locationSelect.value = locationBeforeDamage;
                    }
                    if (locationSelect.value === 'Gudang' && Array.from(locationSelect.options).some((option) => option.value === 'Ruang Penyimpanan')) {
                        locationSelect.value = 'Ruang Penyimpanan';
                    }
                    forceWarehouse = false;
                    if (locationToggle) locationToggle.disabled = false;
                }
                locationOptions.forEach((option) => {
                    if (option.dataset.value === 'Gudang') option.hidden = !isDamaged;
                });
                updateLocationPicker();
            };

            borrowSelect.addEventListener('change', selectBorrower);
            borrowSelect.addEventListener('change', () => {
                updateLocationPicker(true);
                if (forceWarehouse) locationBeforeDamage = locationSelect.value;
                syncLocationWithCondition();
            });
            conditionSelect?.addEventListener('change', syncLocationWithCondition);
            if (locationToggle && locationMenu) {
                locationToggle.addEventListener('click', () => {
                    const isOpening = locationMenu.hidden;
                    locationMenu.hidden = !isOpening;
                    locationToggle.setAttribute('aria-expanded', String(!locationMenu.hidden));
                    locationSearch.setAttribute('aria-expanded', String(!locationMenu.hidden));
                    if (isOpening) {
                        locationSearch.value = '';
                        filterLocationOptions();
                        locationSearch.focus();
                    }
                });
                locationOptions.forEach((option) => {
                    option.addEventListener('click', () => {
                        locationSelect.value = option.dataset.value;
                        updateLocationPicker();
                        locationMenu.hidden = true;
                        locationToggle.setAttribute('aria-expanded', 'false');
                        locationSearch.setAttribute('aria-expanded', 'false');
                        locationToggle.focus();
                    });
                });
                locationSearch.addEventListener('input', filterLocationOptions);
                locationSearch.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        locationMenu.hidden = true;
                        locationToggle.setAttribute('aria-expanded', 'false');
                        locationSearch.setAttribute('aria-expanded', 'false');
                        locationToggle.focus();
                    } else if (event.key === 'Enter') {
                        event.preventDefault();
                        locationOptions.find((option) => !option.hidden)?.click();
                    } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        const visibleOptions = locationOptions.filter((option) => !option.hidden);
                        const firstOption = event.key === 'ArrowDown' ? visibleOptions[0] : visibleOptions[visibleOptions.length - 1];
                        if (firstOption) {
                            event.preventDefault();
                            firstOption.focus();
                        }
                    }
                });
                document.addEventListener('click', (event) => {
                    if (!event.target.closest('.return-location-picker')) {
                        locationMenu.hidden = true;
                        locationToggle.setAttribute('aria-expanded', 'false');
                        locationSearch.setAttribute('aria-expanded', 'false');
                    }
                });
            }
            if (!staffSelect.value) selectBorrower();
            syncLocationWithCondition();
        });
    </script>
@endsection
