@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="mb-0">Daftar Transaksi</h4>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('transactions.export', request()->query()) }}" class="btn btn-outline-secondary">Ekspor CSV</a>
            @if(auth()->user()->role !== 'admin')
                <a href="{{ route('transactions.borrow') }}" class="btn btn-primary">Ambil Barang</a>
                <a href="{{ route('transactions.return') }}" class="btn btn-outline-light">Kembalikan Barang</a>
            @endif
            <a href="{{ route('transactions.transfer') }}" class="btn btn-secondary">Pindahkan Barang</a>
        </div>
    </div>

    <div class="card p-3 mb-4">
        <form class="row g-3" method="GET" action="{{ route('transactions.index') }}">
            <div class="col-md-2">
                <label class="form-label">Jenis</label>
                <select name="type" class="form-select">
                    <option value="">Semua</option>
                    <option value="borrow" {{ request('type') == 'borrow' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Dipindahkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Waktu (Jam)</label>
                <input type="time" name="time" value="{{ request('time') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach(App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary flex-fill" type="submit">Filter</button>
                @if(request()->hasAny(['type', 'date', 'time', 'user_id']))
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-light">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card p-3">
        <div class="table-wrap table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Kode Barang</th>
                        <th>Dipinjam oleh</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Tanggal & Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $status = $transaction->status ?? match ($transaction->type) {
                                'borrow' => 'Dipinjam',
                                'return' => 'Dikembalikan',
                                'transfer' => 'Dipindahkan',
                                default => 'Aktif',
                            };
                            $badgeClass = match ($status) {
                                'Dipinjam' => 'bg-warning text-dark',
                                'Menunggu Persetujuan' => 'bg-secondary',
                                'Dikembalikan' => 'bg-success',
                                'Ditolak' => 'bg-danger',
                                'Dipindahkan' => 'bg-info',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $transaction->item->name ?? '-' }}</td>
                            <td>{{ $transaction->item->code ?? '-' }}</td>
                            <td>{{ $transaction->to_person ?? $transaction->user->name ?? '-' }}</td>
                            <td>{{ $transaction->clean_notes }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="text-nowrap">{{ $transaction->formatted_transaction_date }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-secondary">Detail</a>
                                    @if(auth()->user()->role === 'admin' && $transaction->type === 'borrow')
                                        @if($transaction->status === 'Menunggu Persetujuan')
                                            <form action="{{ route('transactions.approve', $transaction) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                            </form>
                                            <form action="{{ route('transactions.reject', $transaction) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                            </form>
                                        @endif
                                        @if(in_array($transaction->status, ['Menunggu Persetujuan', 'Ditolak'], true))
                                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengajuan pinjaman ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        @endif
                                    @endif
                                    @if($transaction->type === 'borrow' && $transaction->status === 'Dikembalikan' && $transaction->returnTransaction && (auth()->user()->role === 'admin' || $transaction->returnTransaction->user_id === auth()->id()))
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tracking pinjam dan pengembalian yang berpasangan? Stok tidak akan berubah.');">
                                             @csrf
                                             @method('DELETE')
                                             <button type="submit" class="btn btn-sm btn-danger" title="Hapus tracking pengembalian" aria-label="Hapus tracking pengembalian"><span aria-hidden="true">🗑</span></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
