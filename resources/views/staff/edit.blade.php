@extends('layouts.app')

@section('title', 'Edit Staf')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Edit Staf</h4>
        <form action="{{ route('staff.update', $staff) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nama lengkap dan gelar</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $staff->name) }}" required maxlength="255">
            </div>
            <div class="mb-3">
                <label for="is_active" class="form-label">Status staf</label>
                <select id="is_active" name="is_active" class="form-select" required>
                    <option value="1" @selected((string) old('is_active', (int) $staff->is_active) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('is_active', (int) $staff->is_active) === '0')>Nonaktif</option>
                </select>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection