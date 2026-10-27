@extends('layouts.app')

@section('title', 'Tambah Lokasi')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Tambah Lokasi</h4>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Lokasi</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
