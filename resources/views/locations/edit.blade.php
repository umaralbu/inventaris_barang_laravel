@extends('layouts.app')

@section('title', 'Edit Lokasi')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Edit Lokasi</h4>
        <form action="{{ route('locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama Lokasi</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $location->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $location->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="is_active" class="form-label">Status</label>
                <select id="is_active" name="is_active" class="form-select" required>
                    <option value="1" @selected((string) old('is_active', (int) $location->is_active) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('is_active', (int) $location->is_active) === '0')>Arsip</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
