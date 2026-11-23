@extends('layouts.app')

@section('title', 'Barang')

@section('content')
    <style>
        .item-search-picker { position: relative; }
        .item-search-menu { position: absolute; z-index: 1070; top: calc(100% + .25rem); left: 0; right: 0; max-height: 260px; overflow-y: auto; overscroll-behavior-y: contain; scrollbar-gutter: stable; padding: .35rem; border: 1px solid var(--border, #dee2e6); border-radius: 8px; background: var(--card-bg, #fff); color: var(--text); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12); }
        .item-search-menu[hidden], .item-search-option[hidden] { display: none; }
        .item-search-option { display: block; width: 100%; padding: .55rem .7rem; border: 0; border-radius: 6px; background: transparent; color: inherit; text-align: left; overflow-wrap: anywhere; }
        .item-search-option:hover, .item-search-option:focus, .item-search-option.is-active { outline: none; background: var(--primary-soft); }
        .items-pagination nav > .d-none.flex-sm-fill.d-sm-flex { gap: .75rem; }
        .item-status-badge { border-radius: 4px; font-size: .75rem; font-weight: 700; line-height: 1; padding: .4rem .65rem; white-space: nowrap; }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Barang</h4>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('items.export', request()->query()) }}" class="btn btn-outline-secondary">Export CSV</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('items.create') }}" class="btn btn-primary">Tambah Barang</a>
            @endif
        </div>
    </div>

    <div class="card p-3 mb-4">
        <form method="GET" action="{{ route('items.index') }}" class="row g-3">
            <div class="col-md-3">
                <div class="item-search-picker" id="itemSearchPicker">
                    <input type="search" id="itemSearchInput" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama / kode / merek" aria-label="Cari nama, kode, atau merek barang" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="itemSearchMenu" autocomplete="off">
                    <div id="itemSearchMenu" class="item-search-menu" role="listbox" aria-label="Saran barang" hidden>
                        @foreach($searchItems as $searchItem)
                            <button type="button" class="item-search-option" role="option" aria-selected="false" data-value="{{ $searchItem->code }}" data-search="{{ $searchItem->name }} {{ $searchItem->code }} {{ $searchItem->brand }}">
                                <span class="fw-semibold d-block">{{ $searchItem->name }}</span>
                                <span class="small text-secondary">{{ $searchItem->code }} · Merek: {{ $searchItem->brand ?: '-' }}</span>
                            </button>
                        @endforeach
                        <div id="itemSearchEmpty" class="searchable-select-empty" role="status" hidden>Tidak ada barang yang cocok.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select" data-searchable-select data-search-placeholder="" aria-label="Filter kategori">
                    <option value="">Semua Kategori</option>
                    @foreach(
                        App\Models\Category::all() as $category
                    )
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select" data-searchable-select data-search-placeholder="" aria-label="Filter status">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="condition" class="form-select" data-searchable-select data-search-placeholder="" aria-label="Filter kondisi">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" {{ request('condition') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak" {{ request('condition') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="Perlu Perbaikan" {{ request('condition') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="location_id" class="form-select" data-searchable-select data-search-placeholder="" aria-label="Filter lokasi">
                    <option value="">Semua Lokasi</option>
                    @foreach(App\Models\Location::where('is_active', true)->orderBy('name')->get() as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>
    </div>

    <div class="card p-3">
        <div class="table-wrap table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Merek</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Kondisi</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->brand ?: '-' }}</td>
                            <td>{{ $item->category?->name ?? '-' }}</td>
                            @php
                                $statusBadgeClass = match ($item->status) {
                                    'Tersedia' => 'bg-success',
                                    'Dipinjam' => 'bg-warning text-dark',
                                    'Hilang' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <td><span class="badge item-status-badge {{ $statusBadgeClass }}">{{ $item->status }}</span></td>
                            <td>{{ $item->condition }}</td>
                            <td>{{ $item->location?->name ?? '-' }}</td>
                            <td class="text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-secondary">Detail</a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger px-2 btn-delete-item" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteWarningModal" 
                                            data-item-name="{{ $item->name }}" 
                                            data-item-code="{{ $item->code }}" 
                                            data-delete-url="{{ route('items.destroy', $item) }}">
                                        Hapus
                                    </button>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 px-1">
            <div class="items-pagination">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <!-- Modal Konfirmasi Peringatan Hapus Barang (Warning Modal) -->
        <div class="modal fade" id="deleteWarningModal" tabindex="-1" aria-labelledby="deleteWarningModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-2 px-3 border-0">
                        <h6 class="modal-title fw-semibold text-danger d-flex align-items-center gap-2" id="deleteWarningModalLabel">
                            <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus Barang
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body py-2 px-3">
                        <p class="mb-2 text-secondary">Apakah Anda yakin ingin menghapus barang berikut?</p>
                        <div class="p-2 mb-3 bg-light rounded border">
                            <div class="fw-semibold text-dark" id="modalItemCode"></div>
                            <div class="text-secondary small" id="modalItemName"></div>
                        </div>
                        <div class="alert alert-warning py-2 px-3 small mb-0 d-flex align-items-center gap-2 border-0">
                            <i class="bi bi-info-circle-fill fs-5 text-warning flex-shrink-0"></i>
                            <div>
                                Data barang yang dihapus tidak dapat dikembalikan. Jika barang memiliki riwayat transaksi, barang akan diarsipkan secara otomatis.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 border-0">
                        <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                        <form id="deleteItemForm" action="" method="POST" class="m-0 d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger px-3">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteModal = document.getElementById('deleteWarningModal');
                const deleteForm = document.getElementById('deleteItemForm');
                const modalItemCode = document.getElementById('modalItemCode');
                const modalItemName = document.getElementById('modalItemName');

                if (deleteModal) {
                    deleteModal.addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        if (!button) return;
                        modalItemCode.textContent = button.getAttribute('data-item-code') || '';
                        modalItemName.textContent = button.getAttribute('data-item-name') || '';
                        deleteForm.action = button.getAttribute('data-delete-url') || '';
                    });
                }
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const picker = document.getElementById('itemSearchPicker');
            const input = document.getElementById('itemSearchInput');
            const menu = document.getElementById('itemSearchMenu');
            const empty = document.getElementById('itemSearchEmpty');
            if (!picker || !input || !menu || !empty) return;

            const options = [...menu.querySelectorAll('.item-search-option')];
            const close = () => {
                menu.hidden = true;
                input.setAttribute('aria-expanded', 'false');
                options.forEach(option => option.classList.remove('is-active'));
                input.removeAttribute('aria-activedescendant');
            };
            const filter = () => {
                const query = input.value.trim().toLocaleLowerCase('id');
                let visibleCount = 0;
                options.forEach(option => {
                    option.hidden = !option.dataset.search.toLocaleLowerCase('id').includes(query);
                    if (!option.hidden) visibleCount++;
                });
                empty.hidden = visibleCount > 0;
                menu.hidden = false;
                input.setAttribute('aria-expanded', 'true');
            };

            input.addEventListener('focus', filter);
            input.addEventListener('input', filter);
            input.addEventListener('keydown', event => {
                if (event.key === 'Escape' && !menu.hidden) {
                    event.preventDefault();
                    close();
                } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    if (menu.hidden) filter();
                    const visibleOptions = options.filter(option => !option.hidden);
                    const activeOption = event.key === 'ArrowDown' ? visibleOptions[0] : visibleOptions[visibleOptions.length - 1];
                    if (activeOption) {
                        event.preventDefault();
                        activeOption.focus();
                    }
                }
            });
            options.forEach(option => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.value;
                    close();
                    input.focus();
                });
                option.addEventListener('keydown', event => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        close();
                        input.focus();
                    } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        const visibleOptions = options.filter(visibleOption => !visibleOption.hidden);
                        const currentIndex = visibleOptions.indexOf(option);
                        const nextIndex = event.key === 'ArrowDown'
                            ? Math.min(currentIndex + 1, visibleOptions.length - 1)
                            : Math.max(currentIndex - 1, 0);
                        visibleOptions[nextIndex]?.focus();
                    }
                });
            });
            picker.addEventListener('focusout', event => {
                if (!picker.contains(event.relatedTarget)) close();
            });
            document.addEventListener('click', event => {
                if (!picker.contains(event.target)) close();
            });
        });
    </script>
@endsection
