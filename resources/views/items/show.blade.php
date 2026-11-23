@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
    <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Detail Barang</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('items.history', $item) }}" class="btn btn-secondary">Riwayat</a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-primary">Edit</a>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                @if($item->photo)
                    <img src="{{ route('items.photo', $item) }}" class="img-fluid rounded" alt="Foto {{ $item->name }}">
                @else
                    <div class="bg-secondary text-center py-5 rounded">No Photo</div>
                @endif
            </div>
            <div class="col-md-8">
                <table class="table">
                    <tr><th>Kode</th><td>{{ $item->code }}</td></tr>
                    <tr><th>Nama</th><td>{{ $item->name }}</td></tr>
                    <tr><th>Merek</th><td>{{ $item->brand ?: '-' }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $item->category?->name ?? '-' }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $item->location?->name ?? '-' }}</td></tr>
                    @php
                        $statusBadgeClass = match ($item->status) {
                            'Tersedia' => 'bg-success',
                            'Dipinjam' => 'bg-warning text-dark',
                            'Hilang' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <tr><th>Status</th><td><span class="badge {{ $statusBadgeClass }}">{{ $item->status }}</span></td></tr>
                    <tr><th>Kondisi</th><td>{{ $item->condition }}</td></tr>
                    <tr><th>Jumlah</th><td>{{ $item->quantity }} {{ $item->unit }}</td></tr>
                    <tr><th>Tanggal Pembelian</th><td>{{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Harga</th><td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td></tr>
                    @if($item->specification)
                        <tr><th>Spesifikasi</th><td>{{ $item->specification }}</td></tr>
                    @endif
                    @if($item->description)
                        <tr><th>Keterangan</th><td>{{ $item->description }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <h5 class="mb-3">Riwayat Terakhir</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Pengguna</th>
                        <th>Tanggal & Waktu</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->transactions()->latest('transaction_date')->take(10)->get() as $transaction)
                        <tr>
                            <td>{{ match ($transaction->type) {
                                'loss_report' => 'Laporan Kehilangan',
                                'recovery' => 'Barang Ditemukan',
                                default => ucfirst($transaction->type),
                            } }}</td>
                            <td>{{ $transaction->user->name ?? '-' }}</td>
                            <td class="text-nowrap">{{ $transaction->formatted_transaction_date }}</td>
                            <td>{{ $transaction->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
