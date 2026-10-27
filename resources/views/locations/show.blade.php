@extends('layouts.app')

@section('title', 'Detail Lokasi')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Detail Lokasi</h4>
        <table class="table">
            <tr><th>Nama</th><td>{{ $location->name }}</td></tr>
            <tr><th>Deskripsi</th><td>{{ $location->description ?? '-' }}</td></tr>
        </table>
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
