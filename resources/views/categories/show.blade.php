@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Detail Kategori</h4>
        <table class="table">
            <tr><th>Nama</th><td>{{ $category->name }}</td></tr>
            <tr><th>Deskripsi</th><td>{{ $category->description ?? '-' }}</td></tr>
        </table>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
