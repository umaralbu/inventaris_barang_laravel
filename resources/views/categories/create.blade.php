@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Tambah Kategori</h4>
        <form action="{{ route('categories.store') }}" method="POST" autocomplete="off">
            @csrf
            @include('components.name-suggestion-picker', [
                'inputId' => 'categoryName',
                'fieldName' => 'name',
                'label' => 'Nama Kategori',
                'value' => old('name', ''),
                'placeholder' => 'Masukkan nama kategori',
                'searchPlaceholder' => 'Cari nama kategori...',
                'suggestions' => $categoryNames,
            ])
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
