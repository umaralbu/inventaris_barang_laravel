@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="card p-4 mb-4">
        <h4 class="mb-3">Tambah Kategori</h4>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="mb-0">Daftar Kategori</h4>
            <div class="col-12 col-md-5 col-lg-4">
                <label for="categoryListSearch" class="visually-hidden">Cari kategori</label>
                <input type="search" id="categoryListSearch" class="form-control" placeholder="Cari nama atau deskripsi kategori..." aria-label="Cari nama atau deskripsi kategori" aria-controls="categoryList" data-list-search="categoryList" data-empty-message="categoryListSearchEmpty">
            </div>
        </div>
        <div class="table-wrap table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="categoryList">
                    @forelse($categories as $category)
                        <tr data-search="{{ $category->name }} {{ $category->description }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description ?? '-' }}</td>
                            <td>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                    <tr id="categoryListSearchEmpty" hidden>
                        <td colspan="4" class="text-center text-secondary" role="status">Tidak ada kategori yang cocok dengan pencarian.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="{{ asset('js/list-search.js') }}"></script>
@endsection
