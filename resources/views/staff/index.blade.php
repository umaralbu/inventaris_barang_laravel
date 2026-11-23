@extends('layouts.app')

@section('title', 'Staf Perpustakaan')

@section('content')
    <style>
        .staff-search-picker { position: relative; }
        .staff-search-menu { position: absolute; z-index: 1070; top: calc(100% + .25rem); left: 0; right: 0; max-height: 260px; overflow-y: auto; overscroll-behavior-y: contain; scrollbar-gutter: stable; padding: .35rem; border: 1px solid var(--border, #dee2e6); border-radius: 8px; background: var(--card-bg, #fff); color: var(--text); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12); }
        .staff-search-menu[hidden], .staff-search-option[hidden] { display: none; }
        .staff-search-option { display: block; width: 100%; padding: .55rem .7rem; border: 0; border-radius: 6px; background: transparent; color: inherit; text-align: left; overflow-wrap: anywhere; }
        .staff-search-option:hover, .staff-search-option:focus, .staff-search-option.is-active { outline: none; background: var(--primary-soft); }
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h4 class="mb-1">Staf Perpustakaan</h4>
            <p class="text-secondary mb-0">Kelola staf yang dapat dicatat sebagai peminjam barang.</p>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <form action="{{ route('staff.index') }}" method="GET" class="d-flex gap-2" role="search">
                <div class="staff-search-picker" id="staffSearchPicker">
                    <input type="search" id="staffSearchInput" name="search" class="form-control" placeholder="Cari nama staf..." aria-label="Cari nama staf" value="{{ $search }}" maxlength="255" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="staffSearchMenu" autocomplete="off">
                    <div id="staffSearchMenu" class="staff-search-menu" role="listbox" aria-label="Saran staf" hidden>
                    @foreach($staffNames as $staffName)
                            <button type="button" class="staff-search-option" role="option" aria-selected="false" data-value="{{ $staffName }}" data-search="{{ $staffName }}">
                                <span class="fw-semibold d-block">{{ $staffName }}</span>
                            </button>
                        @endforeach
                        <div id="staffSearchEmpty" class="searchable-select-empty" role="status" hidden>Tidak ada staf yang cocok.</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary">Cari</button>
                @if($search !== '')
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
            <a href="{{ route('staff.create') }}" class="btn btn-primary">Tambah Staf</a>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-wrap table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Staf</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $staffMember)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $staffMember->name }}</td>
                            <td>
                                <span class="badge {{ $staffMember->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $staffMember->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('staff.edit', $staffMember) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('staff.destroy', $staffMember) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus staf ini dari daftar? Riwayat transaksi tetap tersimpan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary">{{ $search !== '' ? 'Tidak ada staf yang sesuai dengan pencarian.' : 'Belum ada data staf.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const picker = document.getElementById('staffSearchPicker');
            const input = document.getElementById('staffSearchInput');
            const menu = document.getElementById('staffSearchMenu');
            const empty = document.getElementById('staffSearchEmpty');
            if (!picker || !input || !menu || !empty) return;

            const options = [...menu.querySelectorAll('.staff-search-option')];
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
