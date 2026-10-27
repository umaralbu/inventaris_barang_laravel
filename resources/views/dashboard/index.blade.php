@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if(auth()->user()->role === 'admin')
        <div class="dashboard-hero card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <p class="eyebrow">Ringkasan operasional</p>
                    <h3 class="mb-1">Dashboard Admin</h3>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('items.create') }}" class="btn btn-primary">+ Barang Baru</a>
                </div>
            </div>
        </div>
    @else
        <div class="dashboard-hero card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <p class="eyebrow">Selamat datang</p>
                    <h3 class="mb-1">Dashboard Pegawai</h3>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('items.index') }}" class="btn btn-outline-light">Lihat Barang</a>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($lowStockItems) && $lowStockItems->count())
        <div class="stock-warning-card card mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stock-warning-icon">
                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Peringatan Stok Menipis</h6>
                        <small class="text-secondary">Ada {{ $lowStockItems->count() }} barang yang perlu segera diisi ulang.</small>
                    </div>
                </div>
                <div>
                    <button type="button" class="stock-warning-button" data-bs-toggle="modal" data-bs-target="#lowStockModal">
                        Lihat daftar <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 px-4 pt-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-5" aria-hidden="true"></i>
                            <h5 class="modal-title mb-0" id="lowStockModalLabel">Daftar Stok Menipis</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pb-4 pt-2">
                        <div class="table-wrap table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Stok</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockItems as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td><strong>{{ $item->quantity }}</strong></td>
                                            <td>pcs</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @if(auth()->user()->role === 'admin')
            <div class="col-md-3">
                <div class="stats-card gradient-blue" role="button" tabindex="0" data-stat="total" data-count="{{ $totalBarang }}" data-title="Total Barang" data-description="Jumlah seluruh barang yang tercatat di sistem inventaris saat ini." data-detail="Semua item masuk dalam perhitungan, termasuk barang yang tersedia, dipinjam, rusak, dan hilang." data-items='{{ json_encode($itemsByStatus["total"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Total Barang</div>
                    <h3 class="mt-2 mb-0">{{ $totalBarang }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card gradient-green" role="button" tabindex="0" data-stat="available" data-count="{{ $barangTersedia }}" data-title="Barang Tersedia" data-description="Barang yang siap dipakai atau dipinjam." data-detail="Barang dengan status tersedia dapat diambil untuk penggunaan operasional tanpa kendala." data-items='{{ json_encode($itemsByStatus["available"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Tersedia</div>
                    <h3 class="mt-2 mb-0">{{ $barangTersedia }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card gradient-amber" role="button" tabindex="0" data-stat="borrowed" data-count="{{ $barangDipinjam }}" data-title="Barang Dipinjam" data-description="Barang yang saat ini sedang dipinjam." data-detail="Status ini menunjukkan jumlah item yang telah keluar dari stok utama dan sedang digunakan pihak lain." data-items='{{ json_encode($itemsByStatus["borrowed"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Dipinjam</div>
                    <h3 class="mt-2 mb-0">{{ $barangDipinjam }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card gradient-red" role="button" tabindex="0" data-stat="damaged" data-count="{{ $barangRusak }}" data-title="Barang Rusak" data-description="Barang yang sedang tidak layak pakai." data-detail="Barang rusak perlu diperbaiki, dipindahkan, atau dihapus dari daftar operasional agar tidak dipinjam kembali." data-items='{{ json_encode($itemsByStatus["damaged"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Rusak</div>
                    <h3 class="mt-2 mb-0">{{ $barangRusak }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card gradient-violet" role="button" tabindex="0" data-stat="lost" data-count="{{ $barangHilang }}" data-title="Barang Hilang" data-description="Barang yang tidak ditemukan atau belum diketahui lokasinya." data-detail="Status hilang perlu ditindaklanjuti dengan pengecekan dan dokumentasi untuk menjaga akurasi inventaris." data-items='{{ json_encode($itemsByStatus["lost"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Hilang</div>
                    <h3 class="mt-2 mb-0">{{ $barangHilang }}</h3>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="stats-card gradient-blue" role="button" tabindex="0" data-stat="available" data-count="{{ $barangTersedia }}" data-title="Barang Tersedia" data-description="Barang yang dapat Anda pinjam saat ini." data-detail="Jumlah ini menunjukkan stok barang yang masih siap digunakan untuk kebutuhan operasional Anda.">
                    <div class="text-muted">Barang Tersedia</div>
                    <h3 class="mt-2 mb-0">{{ $barangTersedia }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card gradient-amber" role="button" tabindex="0" data-stat="my-borrowed" data-count="{{ $myBorrowed ?? 0 }}" data-title="Pinjaman Saya" data-description="Jumlah peminjaman yang pernah Anda lakukan." data-detail="Ini mencerminkan total pinjaman aktif maupun sebelumnya yang tercatat atas nama Anda.">
                    <div class="text-muted">Pinjaman Saya</div>
                    <h3 class="mt-2 mb-0">{{ $myBorrowed ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card gradient-green" role="button" tabindex="0" data-stat="active-borrow" data-count="{{ $myActiveBorrow ?? 0 }}" data-title="Aktif Saat Ini" data-description="Jumlah pinjaman Anda yang sedang berjalan." data-detail="Barang yang masih aktif dipinjam dan belum dikembalikan masuk dalam kategori ini.">
                    <div class="text-muted">Aktif Saat Ini</div>
                    <h3 class="mt-2 mb-0">{{ $myActiveBorrow ?? 0 }}</h3>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="statInfoModal" tabindex="-1" aria-labelledby="statInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-bold" id="statInfoModalLabel">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="stat-detail-badge" id="statDetailBadge">0</div>
                    <h4 class="mt-3 mb-2" id="statDetailTitle">Judul</h4>
                    <p class="text-secondary mb-3" id="statDetailDescription">Deskripsi</p>
                    <div id="statDetailInfo" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">{{ auth()->user()->role === 'admin' ? 'Transaksi Terbaru' : 'Riwayat Saya' }}</h5>
        </div>
        <div class="table-wrap table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>{{ auth()->user()->role === 'admin' ? 'Pengguna' : 'Status' }}</th>
                        <th>Tanggal & Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->item->name ?? '-' }}</td>
                            <td>{{ auth()->user()->role === 'admin' ? ($transaction->user->name ?? '-') : ($transaction->status ?? 'Aktif') }}</td>
                            <td class="text-nowrap">{{ $transaction->formatted_transaction_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-secondary">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
