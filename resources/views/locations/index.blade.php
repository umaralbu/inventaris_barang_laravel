@extends('layouts.app')

@section('title', 'Lokasi')

@section('content')
    <div class="card p-4 mb-4">
        <h4 class="mb-3">Tambah Lokasi</h4>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Lokasi</label>
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
        <h4 class="mb-3">Daftar Lokasi</h4>
        <div class="table-wrap table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->description ?? '-' }}</td>
                            <td><span class="badge {{ $location->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $location->is_active ? 'Aktif' : 'Arsip' }}</span></td>
                            <td>
                                <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-secondary">Edit</a>
                                @if($location->is_active)
                                    <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Arsipkan lokasi ini?')">Arsipkan</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">Belum ada lokasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
