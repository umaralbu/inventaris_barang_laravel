@extends('layouts.app')

@section('title', 'Staf Perpustakaan')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h4 class="mb-1">Staf Perpustakaan</h4>
            <p class="text-secondary mb-0">Kelola staf yang dapat dicatat sebagai peminjam barang.</p>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <form action="{{ route('staff.index') }}" method="GET" class="d-flex gap-2" role="search">
                <input type="search" name="search" class="form-control" placeholder="Cari nama staf..." aria-label="Cari nama staf" value="{{ $search }}" maxlength="255">
                <button type="submit" class="btn btn-secondary">Cari</button>
                @if($search !== '')
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
            <a href="{{ route('staff.create') }}" class="btn btn-primary">Tambah Staf</a>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-wrap table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Staf</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $staffMember)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $staffMember->name }}</td>
                            <td>
                                <span class="badge {{ $staffMember->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $staffMember->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('staff.edit', $staffMember) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('staff.destroy', $staffMember) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus staf ini dari daftar? Riwayat transaksi tetap tersimpan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary">{{ $search !== '' ? 'Tidak ada staf yang sesuai dengan pencarian.' : 'Belum ada data staf.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
