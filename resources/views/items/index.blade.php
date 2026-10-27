@extends('layouts.app')

@section('title', 'Barang')

@section('content')
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
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama / kode">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach(
                        App\Models\Category::all() as $category
                    )
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="condition" class="form-select">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" {{ request('condition') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak" {{ request('condition') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="Perlu Perbaikan" {{ request('condition') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Dipindahkan" {{ request('status') == 'Dipindahkan' ? 'selected' : '' }}>Dipindahkan</option>
                    <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="location_id" class="form-select">
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
                            <td>{{ $item->category?->name ?? '-' }}</td>
                            <td><span class="badge bg-info text-dark">{{ $item->status }}</span></td>
                            <td>{{ $item->condition }}</td>
                            <td>{{ $item->location?->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-secondary">Detail</a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus barang ini?')">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 px-1">
            <div class="text-muted small">
                Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} results
            </div>
            <div>
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
