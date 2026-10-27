@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Detail Pengguna</h4>
        <table class="table">
            <tr><th>Nama</th><td>{{ $user->name }}</td></tr>
            <tr><th>Username</th><td>{{ $user->username }}</td></tr>
            <tr><th>Role</th><td>{{ ucfirst($user->role) }}</td></tr>
        </table>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
