@extends('layouts.app')

@section('title', 'Panduan Penggunaan')

@section('content')
    <style>
        .guide-page .btn-guide-primary {
            background-color: #2F3185;
            border-color: #2F3185;
            color: #FFFFFF;
        }

        .guide-page .btn-guide-primary:hover,
        .guide-page .btn-guide-primary:focus-visible,
        .guide-page .btn-guide-primary:active {
            background-color: #25266D;
            border-color: #25266D;
            color: #FFFFFF;
            box-shadow: 0 0 0 0.2rem rgba(47, 49, 133, 0.18);
        }

        .guide-page .btn-guide-outline-primary {
            background-color: #FFFFFF;
            border-color: #2F3185;
            color: #2F3185;
        }

        .guide-page .btn-guide-outline-primary:hover,
        .guide-page .btn-guide-outline-primary:focus-visible,
        .guide-page .btn-guide-outline-primary:active {
            background-color: #2F3185;
            border-color: #2F3185;
            color: #FFFFFF;
            box-shadow: 0 0 0 0.2rem rgba(47, 49, 133, 0.18);
        }
    </style>

    <div class="guide-page">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4 flex-wrap">
        <div>
            <h4 class="mb-1">Panduan Penggunaan</h4>
            <p class="text-secondary mb-0">Petunjuk lengkap menggunakan web Inventaris &amp; Aset Perpustakaan UMS.</p>
        </div>
    </div>

    <section class="card p-4 mb-4 d-print-none" aria-label="Navigasi panduan">
        <h5 class="mb-3">Mulai dari sini</h5>
        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user()->role === 'admin')
                <a class="btn btn-sm btn-guide-outline-primary" href="#kelola-inventaris">Kelola inventaris</a>
                <a class="btn btn-sm btn-guide-outline-primary" href="#kelola-transaksi">Kelola transaksi</a>
                <a class="btn btn-sm btn-guide-outline-primary" href="#kelola-akun">Kelola akun</a>
                <a class="btn btn-sm btn-guide-outline-primary" href="#analitik-dashboard">Analitik</a>
            @else
                <a class="btn btn-sm btn-guide-outline-primary" href="#mencari-barang">Cari barang</a>
                <a class="btn btn-sm btn-guide-outline-primary" href="#mengajukan-peminjaman">Ajukan peminjaman</a>
                <a class="btn btn-sm btn-guide-outline-primary" href="#mengembalikan-barang">Kembalikan barang</a>
            @endif
            <a class="btn btn-sm btn-guide-outline-primary" href="#status-transaksi">Arti status</a>
            <a class="btn btn-sm btn-guide-outline-primary" href="#pertanyaan">Pertanyaan umum</a>
        </div>
    </section>

    @if(auth()->user()->role === 'admin')
        <section class="card p-4 mb-4" aria-labelledby="adminGuideHeading">
            <h5 id="adminGuideHeading" class="mb-3">Panduan Admin</h5>
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div id="kelola-inventaris" class="h-100 border rounded-3 p-3">
                        <h6>Kelola inventaris</h6>
                        <ol class="mb-3">
                            <li>Buka <strong>Data Barang</strong> untuk melihat, mencari, memfilter, atau membuka detail barang.</li>
                            <li>Pilih <strong>Tambah Barang</strong>, lalu isi nama, kode, merek, kategori, lokasi asal, kondisi, satuan, dan jumlah yang tersedia.</li>
                            <li>Simpan data. Gunakan tombol <strong>Edit</strong> pada daftar untuk memperbarui barang atau <strong>Detail</strong> untuk melihat riwayatnya.</li>
                        </ol>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('items.index') }}">Data Barang</a>
                            <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('categories.index') }}">Kategori</a>
                            <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('locations.index') }}">Lokasi</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div id="kelola-akun" class="h-100 border rounded-3 p-3">
                        <h6>Kelola pengguna dan staf</h6>
                        <ol class="mb-3">
                            <li>Buka <strong>Pengaturan Akses</strong> pada sidebar.</li>
                            <li>Gunakan <strong>Pengguna</strong> untuk menambah, mengubah, atau menghapus akun beserta perannya.</li>
                            <li>Gunakan <strong>Data Staf</strong> untuk mengelola nama staf yang dipilih saat barang diambil.</li>
                        </ol>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('users.index') }}">Pengguna</a>
                            <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('staff.index') }}">Data Staf</a>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div id="kelola-transaksi" class="border rounded-3 p-3">
                        <h6>Kelola transaksi</h6>
                        <ol class="mb-3">
                            <li>Buka <strong>Transaksi</strong>, lalu gunakan filter jenis, tanggal, waktu, atau pengguna untuk mencari aktivitas.</li>
                            <li>Tinjau pengajuan berstatus <strong>Menunggu Persetujuan</strong>. Pilih <strong>Setujui</strong> untuk memproses peminjaman atau <strong>Tolak</strong> untuk menolaknya.</li>
                            <li>Pantau pinjaman aktif dan tanggal jatuh tempo pada kolom status dan jatuh tempo.</li>
                            <li>Gunakan <strong>Pindahkan Barang</strong> untuk mencatat perpindahan unit ke lokasi lain beserta keterangan.</li>
                            <li>Tinjau laporan hilang. Pilih <strong>Konfirmasi Hilang</strong> jika barang benar-benar hilang, atau <strong>Tolak Laporan</strong> jika pinjaman tetap aktif.</li>
                        </ol>
                        <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('transactions.index') }}">Buka Transaksi</a>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded-3 p-3">
                        <h6>Dashboard</h6>
                        <p class="mb-0">Gunakan kartu ringkasan untuk melihat rincian barang berdasarkan status. Klik kartu untuk membuka detail ringkasnya. Bagian Transaksi Terbaru menampilkan aktivitas terkini.</p>
                    </div>
                </div>
                <div class="col-12">
                    <div id="analitik-dashboard" class="border rounded-3 p-3">
                        <h6>Analitik inventaris</h6>
                        <p>Di dashboard admin, pilih <strong>Lihat Analitik</strong> untuk membuka ringkasan dan grafik aktivitas inventaris. Halaman ini menampilkan total nilai barang aktif, jumlah peminjaman pada periode terpilih, barang yang sedang dipinjam, barang tersedia, grafik peminjaman per bulan, dan hingga lima barang yang paling sering dipinjam.</p>
                        <ol class="mb-3">
                            <li>Pada bagian <strong>Peminjaman Berdasarkan Waktu</strong>, pilih periode 6, 12, atau 24 bulan.</li>
                            <li>Pilih <strong>Terapkan</strong> untuk memperbarui jumlah peminjaman dan peringkat barang sesuai periode tersebut.</li>
                            <li>Jika belum ada aktivitas pada periode pilihan, grafik atau daftar peringkat akan menampilkan keterangan bahwa data belum tersedia.</li>
                        </ol>
                        <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('admin.analytics') }}">Buka Analitik</a>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="card p-4 mb-4" aria-labelledby="userGuideHeading">
            <h5 id="userGuideHeading" class="mb-3">Panduan Pengguna</h5>
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div id="mencari-barang" class="h-100 border rounded-3 p-3">
                        <h6>Mencari barang</h6>
                        <ol class="mb-3">
                            <li>Buka <strong>Data Barang</strong> dari sidebar.</li>
                            <li>Gunakan pencarian nama, kode, atau merek, dan filter kategori, status, kondisi, atau lokasi.</li>
                            <li>Pilih <strong>Detail</strong> untuk melihat kondisi, lokasi, jumlah, dan riwayat transaksi barang.</li>
                        </ol>
                        <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('items.index') }}">Cari Barang</a>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div id="mengajukan-peminjaman" class="h-100 border rounded-3 p-3">
                        <h6>Mengajukan peminjaman</h6>
                        <ol class="mb-0">
                            <li>Buka menu <strong>Transaksi</strong>, lalu pilih <strong>Ambil Barang</strong>.</li>
                            <li>Pilih staf dan satu atau beberapa barang yang tersedia.</li>
                            <li>Untuk setiap barang, tentukan lokasi tujuan, keperluan, serta tanggal dan waktu jatuh tempo.</li>
                            <li>Periksa tanggal dan waktu transaksi, lalu pastikan jumlah serta lokasi sudah benar.</li>
                            <li>Pilih <strong>Simpan</strong>. Pengajuan akan berstatus <strong>Menunggu Persetujuan</strong> sampai ditinjau admin.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="h-100 border rounded-3 p-3">
                        <h6>Melihat status dan riwayat</h6>
                        <p class="mb-3">Buka <strong>Transaksi</strong> untuk melihat status, tanggal jatuh tempo, dan detail pengajuan. Gunakan filter untuk mempersempit pencarian. Dashboard menampilkan ringkasan pinjaman dan transaksi terbaru.</p>
                        <a class="btn btn-sm btn-guide-outline-primary" href="{{ route('transactions.index') }}">Lihat Transaksi</a>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div id="mengembalikan-barang" class="h-100 border rounded-3 p-3">
                        <h6>Mengembalikan barang</h6>
                        <ol class="mb-0">
                            <li>Dari menu <strong>Transaksi</strong>, pilih <strong>Kembalikan Barang</strong>.</li>
                            <li>Pilih pinjaman aktif, staf yang menyerahkan barang, lokasi pengembalian, dan kondisi barang.</li>
                            <li>Periksa tanggal dan waktu pengembalian, isi keterangan bila diperlukan, lalu pilih <strong>Simpan</strong>.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded-3 p-3">
                        <h6>Melaporkan barang hilang</h6>
                        <ol class="mb-0">
                            <li>Buka detail pinjaman yang masih berstatus <strong>Dipinjam</strong>.</li>
                            <li>Pilih <strong>Laporkan Hilang</strong> dan isi keterangan laporan.</li>
                            <li>Pinjaman tetap aktif sampai admin mengonfirmasi atau menolak laporan kehilangan.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="card p-4 mb-4" aria-labelledby="importantInfoHeading">
        <h5 id="importantInfoHeading" class="mb-3">Informasi penting</h5>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="h-100 border rounded-3 p-3">
                    <h6>Gunakan filter untuk mempercepat pencarian</h6>
                    <p class="mb-0">Pada Data Barang dan Transaksi, gunakan pencarian serta filter untuk menampilkan data yang sesuai. Pilih <strong>Reset</strong> atau hapus filter untuk kembali ke daftar lengkap.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="h-100 border rounded-3 p-3">
                    <h6>Periksa barang sebelum diproses</h6>
                    <p class="mb-0">Pastikan nama, kode, jumlah, kondisi, lokasi, staf, keperluan, tanggal, dan waktu sudah benar sebelum menyimpan pengajuan atau pengembalian.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="h-100 border rounded-3 p-3">
                    <h6>Ekspor data bila diperlukan</h6>
                    <p class="mb-0"><strong>Ekspor CSV</strong> tersedia pada Data Barang dan Daftar Transaksi untuk menyimpan daftar hasil pencarian atau filter sebagai file spreadsheet.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="status-transaksi" class="card p-4 mb-4" aria-labelledby="statusHeading">
        <h5 id="statusHeading" class="mb-3">Arti status transaksi</h5>
        <div class="table-responsive">
            <table class="table mb-0">
                <tbody>
                    <tr><th class="w-25">Menunggu Persetujuan</th><td>Pengajuan peminjaman sudah dibuat dan belum ditinjau admin.</td></tr>
                    <tr><th>Dipinjam</th><td>Pengajuan disetujui dan barang sedang berada pada peminjam.</td></tr>
                    <tr><th>Dikembalikan</th><td>Barang sudah dicatat kembali beserta lokasi dan kondisinya.</td></tr>
                    <tr><th>Ditolak</th><td>Pengajuan peminjaman tidak disetujui admin.</td></tr>
                    <tr><th>Laporan hilang menunggu konfirmasi</th><td>Pengguna telah mengirim laporan dan admin belum menentukan hasilnya.</td></tr>
                    <tr><th>Hilang</th><td>Laporan kehilangan telah dikonfirmasi admin.</td></tr>
                    <tr><th>Dipindahkan</th><td>Barang telah dicatat berpindah ke lokasi lain.</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <section id="pertanyaan" class="card p-4 mb-4" aria-labelledby="faqHeading">
        <h5 id="faqHeading" class="mb-3">Pertanyaan umum</h5>
        <div class="accordion" id="guideFaq">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqApproval">Mengapa pengajuan peminjaman belum dapat digunakan?</button>
                </h2>
                <div id="faqApproval" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                    <div class="accordion-body">Status <strong>Menunggu Persetujuan</strong> harus ditinjau oleh admin. Hubungi admin jika pengajuan tidak segera diproses.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOverdue">Bagaimana mengetahui pinjaman yang terlambat?</button>
                </h2>
                <div id="faqOverdue" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                    <div class="accordion-body">Buka <strong>Transaksi</strong> dan periksa kolom jatuh tempo. Pinjaman yang melewati batas akan ditandai <strong>Terlambat</strong>.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAvailable">Mengapa barang yang dicari tidak muncul saat mengajukan peminjaman?</button>
                </h2>
                <div id="faqAvailable" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                    <div class="accordion-body">Form peminjaman hanya menampilkan barang yang berstatus tersedia dan memiliki stok. Periksa status, kondisi, dan jumlah barang pada halaman <strong>Data Barang</strong>.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqMultiple">Bisakah meminjam beberapa barang sekaligus?</button>
                </h2>
                <div id="faqMultiple" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                    <div class="accordion-body">Ya. Pada form <strong>Ambil Barang</strong>, pilih satu atau beberapa barang tersedia, lalu tentukan lokasi tujuan, keperluan, dan jatuh tempo untuk setiap barang.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqReturn">Bagaimana cara mengembalikan barang?</button>
                </h2>
                <div id="faqReturn" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                    <div class="accordion-body">Buka <strong>Transaksi</strong>, pilih <strong>Kembalikan Barang</strong>, lalu pilih pinjaman aktif, staf yang menyerahkan, lokasi pengembalian, kondisi, tanggal, dan waktu.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqReturnCondition">Apa yang terjadi jika barang dikembalikan dalam kondisi rusak?</button>
                </h2>
                <div id="faqReturnCondition" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                    <div class="accordion-body">Pilih kondisi <strong>Rusak</strong> atau <strong>Perlu Perbaikan</strong> pada form pengembalian. Tambahkan keterangan agar admin mengetahui kondisi barang dengan jelas.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqLost">Barang hilang, apa yang harus dilakukan?</button>
                </h2>
                <div id="faqLost" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                    <div class="accordion-body">Buka detail pinjaman berstatus <strong>Dipinjam</strong>, pilih <strong>Laporkan Hilang</strong>, dan isi keterangan. Laporan akan ditinjau admin sebelum status pinjaman diubah.</div>
                </div>
            </div>
            @if(auth()->user()->role === 'admin')
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnalytics">Apa saja informasi pada halaman Analitik?</button>
                    </h2>
                    <div id="faqAnalytics" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                        <div class="accordion-body">Halaman Analitik merangkum nilai inventaris aktif, peminjaman pada periode pilihan, barang yang sedang dipinjam, barang tersedia, grafik peminjaman bulanan, dan hingga lima barang paling sering dipinjam.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnalyticsPeriod">Bagaimana mengubah rentang waktu analitik?</button>
                    </h2>
                    <div id="faqAnalyticsPeriod" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                        <div class="accordion-body">Pilih periode <strong>6 bulan</strong>, <strong>12 bulan</strong>, atau <strong>24 bulan</strong> pada bagian grafik peminjaman, lalu tekan <strong>Terapkan</strong>. Ringkasan peminjaman dan peringkat barang akan mengikuti periode pilihan.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqManageItem">Bagaimana menambah atau mengubah data barang?</button>
                    </h2>
                    <div id="faqManageItem" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                        <div class="accordion-body">Buka <strong>Data Barang</strong>, pilih <strong>Tambah Barang</strong> untuk mencatat barang baru, atau pilih <strong>Edit</strong> pada daftar untuk mengubah data yang sudah ada.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqApprove">Kapan pengajuan pinjaman disetujui atau ditolak?</button>
                    </h2>
                    <div id="faqApprove" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                        <div class="accordion-body">Buka detail atau daftar <strong>Transaksi</strong> untuk pengajuan berstatus <strong>Menunggu Persetujuan</strong>. Pilih <strong>Setujui</strong> atau <strong>Tolak</strong> setelah memeriksa data pengajuan.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTransfer">Kapan menggunakan Pindahkan Barang?</button>
                    </h2>
                    <div id="faqTransfer" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                        <div class="accordion-body">Gunakan <strong>Pindahkan Barang</strong> pada menu Transaksi bila unit barang dipindahkan ke lokasi lain. Isi barang, lokasi asal dan tujuan, staf, tanggal, waktu, serta keterangan.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqManageCategory">Bagaimana mengelola kategori dan lokasi?</button>
                    </h2>
                    <div id="faqManageCategory" class="accordion-collapse collapse" data-bs-parent="#guideFaq">
                        <div class="accordion-body">Buka menu <strong>Kategori</strong> untuk menambah atau mengubah kategori, dan menu <strong>Lokasi</strong> untuk mengelola lokasi barang beserta status aktifnya.</div>
                    </div>
                </div>
            @endif
        </div>
    </section>
    </div>
@endsection
