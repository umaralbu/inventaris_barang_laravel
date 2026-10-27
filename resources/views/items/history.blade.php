@extends('layouts.app')

@section('title', 'Riwayat Barang')

@section('content')
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Riwayat Barang: {{ $item->code }} - {{ $item->name }}</h4>
            <a href="{{ route('items.show', $item) }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Jenis</th>
                        <th>Dari</th>
                        <th>Ke</th>
                        <th>Oleh</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->transactions()->orderByDesc('transaction_date')->get() as $transaction)
                        <tr>
                            <td class="text-nowrap">{{ $transaction->formatted_transaction_date }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                            <td>{{ $transaction->from_location ?? $transaction->from_person ?? '-' }}</td>
                            <td>{{ $transaction->to_location ?? $transaction->to_person ?? '-' }}</td>
                            <td>{{ $transaction->user->name ?? '-' }}</td>
                            <td>{{ $transaction->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
