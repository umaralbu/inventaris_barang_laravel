@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    @php
        $status = $transaction->status ?? match ($transaction->type) {
            'borrow' => 'Dipinjam',
            'return' => 'Dikembalikan',
            'transfer' => 'Dipindahkan',
            default => 'Aktif',
        };
        $badgeClass = match ($status) {
            'Dipinjam' => 'bg-warning text-dark',
            'Dikembalikan' => 'bg-success',
            'Ditolak' => 'bg-danger',
            'Dipindahkan' => 'bg-info',
            default => 'bg-secondary',
        };
    @endphp
    <div class="card p-4">
        <h4 class="mb-3">Detail Transaksi</h4>
        <table class="table">
            <tr><th>Barang</th><td>{{ $transaction->item->name ?? '-' }}</td></tr>
            <tr><th>Kode Barang</th><td>{{ $transaction->item->code ?? '-' }}</td></tr>
            <tr><th>Dipinjam oleh</th><td>{{ $transaction->to_person ?? $transaction->user->name ?? '-' }}</td></tr>
            <tr><th>Oleh admin</th><td>{{ $transaction->user->name ?? '-' }}</td></tr>
            <tr><th>Dari lokasi</th><td>{{ $transaction->from_location ?? $transaction->from_person ?? '-' }}</td></tr>
            <tr><th>Keperluan</th><td>{{ $transaction->clean_notes }}</td></tr>
            <tr><th>Status</th>
                <td>
                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                </td>
            </tr>
            <tr><th>Tanggal & Waktu</th><td>{{ $transaction->formatted_transaction_date }}</td></tr>
            @if($transaction->returnTransaction)
                <tr><th>Tanggal & Waktu Pengembalian</th><td>{{ $transaction->returnTransaction->formatted_transaction_date }}</td></tr>
            @endif
        </table>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
