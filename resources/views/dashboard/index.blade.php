@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if(auth()->user()->role === 'admin')
        <div class="dashboard-hero dashboard-hero-centered card p-4 mb-4">
            <div class="w-100 text-center">
                <div>
                    <p class="eyebrow"><i class="bi bi-journal-bookmark-fill me-1" aria-hidden="true"></i> Perpustakaan Universitas Muhammadiyah Surakarta</p>
                    <h3 class="mb-1">Inventaris &amp; Aset Perpustakaan</h3>
                    <p class="dashboard-hero-copy">Pantau barang untuk ruang baca, referensi, sirkulasi, pengolahan, dan ruang pendukung.</p>
                    <a href="{{ route('admin.analytics') }}" class="btn btn-outline-light mt-3">
                        <i class="bi bi-bar-chart-line me-1" aria-hidden="true"></i> Lihat Analitik
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="dashboard-hero dashboard-hero-centered card p-4 mb-4">
            <div class="w-100 text-center">
                <div>
                    <p class="eyebrow"><i class="bi bi-journal-bookmark-fill me-1" aria-hidden="true"></i> Perpustakaan Universitas Muhammadiyah Surakarta</p>
                    <h3 class="mb-1">Layanan Barang Perpustakaan</h3>
                    <p class="dashboard-hero-copy">Cari dan ajukan peminjaman aset untuk mendukung kegiatan layanan perpustakaan.</p>
                </div>
            </div>
        </div>
    @endif

    @include('transactions.partials.overdue-notice')

    <div class="row g-3 mb-4">
        @if(auth()->user()->role === 'admin')
            <div class="col-6 col-lg">
                <div class="stats-card gradient-blue" role="button" tabindex="0" data-stat="total" data-count="{{ $totalBarang }}" data-title="Total Barang" data-description="Jumlah seluruh barang yang tercatat di sistem inventaris saat ini." data-detail="Semua item masuk dalam perhitungan, termasuk barang yang tersedia, dipinjam, rusak, dan hilang." data-items='{{ json_encode($itemsByStatus["total"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Total Barang</div>
                    <h3 class="mt-2 mb-0">{{ $totalBarang }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="stats-card gradient-green" role="button" tabindex="0" data-stat="available" data-count="{{ $barangTersedia }}" data-title="Barang Tersedia" data-description="Barang yang siap dipakai atau dipinjam." data-detail="Barang dengan status tersedia dapat diambil untuk penggunaan operasional tanpa kendala." data-items='{{ json_encode($itemsByStatus["available"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Tersedia</div>
                    <h3 class="mt-2 mb-0">{{ $barangTersedia }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="stats-card gradient-amber" role="button" tabindex="0" data-stat="borrowed" data-count="{{ $barangDipinjam }}" data-title="Barang Dipinjam" data-description="Barang yang saat ini sedang dipinjam." data-detail="Status ini menunjukkan jumlah item yang telah keluar dari stok utama dan sedang digunakan pihak lain." data-items='{{ json_encode($itemsByStatus["borrowed"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Dipinjam</div>
                    <h3 class="mt-2 mb-0">{{ $barangDipinjam }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="stats-card gradient-red" role="button" tabindex="0" data-stat="damaged" data-count="{{ $barangRusak }}" data-title="Barang Rusak" data-description="Barang yang sedang tidak layak pakai." data-detail="Barang rusak perlu diperbaiki, dipindahkan, atau dihapus dari daftar operasional agar tidak dipinjam kembali." data-items='{{ json_encode($itemsByStatus["damaged"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Rusak</div>
                    <h3 class="mt-2 mb-0">{{ $barangRusak }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="stats-card gradient-violet" role="button" tabindex="0" data-stat="lost" data-count="{{ $barangHilang }}" data-title="Barang Hilang" data-description="Barang yang tidak ditemukan atau belum diketahui lokasinya." data-detail="Status hilang perlu ditindaklanjuti dengan pengecekan dan dokumentasi untuk menjaga akurasi inventaris." data-items='{{ json_encode($itemsByStatus["lost"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Hilang</div>
                    <h3 class="mt-2 mb-0">{{ $barangHilang }}</h3>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="stats-card gradient-blue" role="button" tabindex="0" data-stat="available" data-count="{{ $barangTersedia }}" data-title="Barang Tersedia" data-description="Barang yang dapat dipinjam saat ini." data-detail="Jumlah ini menunjukkan stok barang yang masih siap digunakan untuk kebutuhan operasional." data-items='{{ json_encode($itemsByStatus["available"]->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Barang Tersedia</div>
                    <h3 class="mt-2 mb-0">{{ $barangTersedia }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card gradient-amber" role="button" tabindex="0" data-stat="my-borrowed" data-count="{{ $myBorrowed ?? 0 }}" data-title="Pinjaman" data-description="Jumlah peminjaman yang pernah dilakukan." data-detail="Ini mencerminkan total pinjaman aktif maupun sebelumnya yang tercatat." data-items='{{ json_encode($myBorrowedItems->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
                    <div class="text-muted">Pinjaman</div>
                    <h3 class="mt-2 mb-0">{{ $myBorrowed ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card gradient-green" role="button" tabindex="0" data-stat="active-borrow" data-count="{{ $myActiveBorrow ?? 0 }}" data-title="Aktif Saat Ini" data-description="Jumlah pinjaman yang sedang berjalan." data-detail="Barang yang masih aktif dipinjam dan belum dikembalikan masuk dalam kategori ini." data-items='{{ json_encode($myActiveBorrowItems->map(fn ($item) => ["name" => $item->name, "quantity" => $item->quantity, "unit" => $item->unit])) }}'>
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
            <h5 class="mb-0">{{ auth()->user()->role === 'admin' ? 'Transaksi Terbaru' : 'Riwayat' }}</h5>
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
