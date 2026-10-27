@extends('layouts.app')

@section('title', 'Tambah Staf')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Tambah Staf</h4>
        <form action="{{ route('staff.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama lengkap dan gelar</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection