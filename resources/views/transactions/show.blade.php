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
            'Dilaporkan Hilang' => 'bg-warning text-dark',
            'Hilang' => 'bg-dark',
            'Ditemukan' => 'bg-success',
            'Ditolak' => 'bg-danger',
            'Dipindahkan' => 'bg-info',
            default => 'bg-secondary',
        };
    @endphp
    <div class="card p-4">
        <h4 class="mb-3">Detail Transaksi</h4>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
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
            @if($transaction->type === 'borrow' && $transaction->due_date)
                @if($transaction->loan_duration_days)
                    <tr><th>Durasi Peminjaman</th><td>{{ $transaction->loan_duration_days }} hari</td></tr>
                @endif
                <tr><th>Tanggal & Waktu Jatuh Tempo</th><td>{{ $transaction->formatted_due_date }} @if($transaction->is_overdue)<span class="badge bg-danger ms-2">Terlambat</span>@endif</td></tr>
            @endif
            @if($transaction->returnTransaction)
                <tr><th>Tanggal & Waktu Pengembalian</th><td>{{ $transaction->returnTransaction->formatted_transaction_date }}</td></tr>
            @endif
        </table>
        @if($transaction->type === 'borrow' && $transaction->lossReports->isNotEmpty())
            <div class="mb-4">
                <h5 class="mb-3">Laporan Kehilangan</h5>
                @foreach($transaction->lossReports as $report)
                    @php
                        $reportBadgeClass = match ($report->status) {
                            'Menunggu Konfirmasi' => 'bg-warning text-dark',
                            'Dikonfirmasi' => 'bg-dark',
                            'Ditolak' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <div class="border rounded-3 p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <strong>{{ $report->user->name ?? $report->from_person ?? 'Pengguna' }}</strong>
                            <span class="badge {{ $reportBadgeClass }}">{{ $report->status }}</span>
                        </div>
                        <div class="small text-secondary mb-2">{{ $report->formatted_transaction_date }}</div>
                        <p class="mb-0">{{ $report->notes }}</p>
                    </div>

                    @if(auth()->user()->role === 'admin' && $report->status === 'Menunggu Konfirmasi')
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <form action="{{ route('transactions.confirm-lost', $report) }}" method="POST" onsubmit="return confirm('Konfirmasi barang hilang? Pinjaman akan ditutup sebagai hilang, bukan dikembalikan.');">
                                @csrf
                                <button type="submit" class="btn btn-warning">Konfirmasi Kehilangan</button>
                            </form>
                            <form action="{{ route('transactions.reject-lost', $report) }}" method="POST" onsubmit="return confirm('Tolak laporan kehilangan ini? Pinjaman akan tetap aktif.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">Tolak Laporan</button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @if($transaction->type === 'borrow'
            && $transaction->status === 'Dipinjam'
            && auth()->user()->role === 'user'
            && $transaction->user_id === auth()->id()
            && !$transaction->lossReports->contains(fn ($report) => $report->status === 'Menunggu Konfirmasi'))
            <div class="alert alert-warning">
                <strong>Barang tidak ditemukan?</strong>
                <div class="small mt-1">Laporkan kehilangan untuk ditinjau admin. Pinjaman tetap aktif sampai laporan dikonfirmasi dan tidak akan dicatat sebagai pengembalian.</div>
            </div>
            <form action="{{ route('transactions.report-lost', $transaction) }}" method="POST" class="border rounded-3 p-3 mb-3">
                @csrf
                <label for="lossReportNotes" class="form-label fw-semibold">Keterangan kehilangan</label>
                <textarea id="lossReportNotes" name="notes" class="form-control mb-2" rows="3" maxlength="2000" required>{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror
                <button type="submit" class="btn btn-warning" onclick="return confirm('Kirim laporan kehilangan ini untuk ditinjau admin?');">Laporkan Hilang</button>
            </form>
        @endif
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
