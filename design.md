# UI/UX REFINEMENT — UMS BRAND GUIDELINE

Lakukan perbaikan dan penyempurnaan UI/UX pada seluruh halaman aplikasi yang sudah ada. Pertahankan seluruh fungsionalitas, alur kerja, fitur, integrasi backend, dan struktur data yang sudah berjalan. Fokus utama adalah meningkatkan kualitas visual agar terlihat profesional, modern, minimalis, bersih, dan tidak berlebihan.

## 1. Brand Identity & Color Palette

Gunakan warna resmi Universitas Muhammadiyah Surakarta (UMS) berikut sebagai acuan utama:

- Primary Color — Biru Tua: `#2F3185`
- Secondary Color — Kuning: `#FFB800`
- Accent Color — Toska: `#1CBDB3`
- Background — Putih: `#FFFFFF`

Aturan penggunaan warna:
- Biru tua menjadi warna dominan untuk elemen utama, navigasi, tombol primary, dan identitas aplikasi.
- Kuning hanya digunakan sebagai aksen untuk highlight, indikator, atau elemen yang perlu mendapatkan perhatian.
- Toska digunakan sebagai aksen pendukung, misalnya status aktif, informasi positif, dan elemen interaktif tertentu.
- Putih atau warna netral yang sangat terang digunakan sebagai background utama.
- Gunakan warna netral seperti abu-abu untuk border, divider, teks sekunder, dan background alternatif.

HINDARI:
- Penggunaan gradient atau gradasi warna dalam bentuk apa pun.
- Gradient pada sidebar, navbar, button, card, background, maupun elemen dekoratif.
- Kombinasi warna yang terlalu mencolok.
- Penggunaan warna kuning dan toska secara berlebihan.
- Warna tambahan yang tidak sesuai dengan identitas visual UMS.

Gunakan solid color, clean contrast, dan konsistensi warna di seluruh halaman.

## 2. Typography

Gunakan typography sesuai pedoman visual UMS:

- Poppins Bold: Heading, judul halaman, dan elemen penting.
- Poppins Medium: Navigasi, label, tombol, dan teks dengan penekanan.
- Montserrat Black: Opsional untuk heading khusus atau elemen branding yang memang membutuhkan karakter kuat.
- Montserrat Medium: Opsional untuk teks pendukung tertentu.

Ketentuan:
- Utamakan konsistensi ukuran dan ketebalan font.
- Hindari terlalu banyak variasi font weight.
- Pastikan keterbacaan teks tetap baik.
- Gunakan hierarki typography yang jelas antara heading, subheading, body text, dan caption.

## 3. Iconography — Minimalist Approach

Hilangkan penggunaan ikon yang berlebihan, tidak relevan, terlalu dekoratif, atau terlihat kekanak-kanakan.

Aturan:
- Gunakan ikon sederhana, minimalis, dan konsisten.
- Prioritaskan ikon dari Lucide Icons.
- Gunakan ukuran ikon yang proporsional.
- Gunakan satu gaya ikon yang konsisten di seluruh aplikasi.
- Jangan menggunakan emoji sebagai pengganti ikon UI.
- Hindari ikon berukuran besar hanya untuk dekorasi.
- Jangan menambahkan ikon pada setiap teks atau elemen yang sebenarnya tidak membutuhkannya.

Ikon hanya digunakan jika membantu pengguna memahami fungsi suatu elemen, seperti navigasi, aksi, status, atau informasi.

## 4. Layout & Component Design

Perbaiki seluruh komponen UI dengan pendekatan clean and minimal design.

Perhatikan:
- Konsistensi spacing dan alignment.
- Padding dan margin yang proporsional.
- Card dengan desain sederhana dan rapi.
- Border radius yang konsisten, tidak terlalu membulat.
- Border tipis dengan warna netral.
- Shadow sangat halus jika memang diperlukan.
- Tombol dengan desain solid dan jelas.
- Input form yang bersih dan mudah digunakan.
- Tabel dengan struktur yang rapi dan mudah dibaca.
- Sidebar dan navbar yang sederhana serta profesional.

Hindari:
- Terlalu banyak card di dalam card.
- Shadow yang terlalu tebal.
- Border radius berlebihan.
- Elemen dekoratif yang tidak memiliki fungsi.
- Layout yang terlalu padat atau terlalu banyak ruang kosong tanpa tujuan.

## 5. Dashboard & Data Visualization

Khusus halaman dashboard:
- Tampilkan informasi statistik dengan hierarki visual yang jelas.
- Gunakan card statistik yang sederhana.
- Gunakan warna brand UMS secara proporsional.
- Grafik dan chart harus mudah dibaca.
- Hindari gradient pada chart.
- Hindari ilustrasi atau ikon dekoratif yang tidak diperlukan.

Prioritaskan informasi dan data, bukan dekorasi visual.

## 6. Konsistensi Seluruh Halaman

Terapkan desain yang konsisten pada seluruh halaman aplikasi, termasuk:
- Dashboard
- Halaman data dan manajemen
- Halaman tabel
- Halaman tambah dan edit data
- Halaman detail
- Halaman transaksi
- Halaman login dan autentikasi
- Modal, dropdown, toast, dan notifikasi

Jangan hanya memperbaiki dashboard atau halaman tertentu. Pastikan seluruh halaman memiliki identitas visual yang sama.

## 7. Technical Constraints

Sebelum melakukan perubahan:
1. Periksa struktur project dan teknologi frontend yang digunakan.
2. Identifikasi komponen global, layout, stylesheet, dan reusable components.
3. Periksa halaman-halaman yang sudah tersedia.
4. Gunakan kembali komponen yang sudah ada jika memungkinkan.

Saat melakukan implementasi:
- Jangan menghapus fitur yang sudah berjalan.
- Jangan mengubah business logic atau backend tanpa alasan yang benar-benar diperlukan.
- Jangan mengubah route, API endpoint, database schema, maupun autentikasi.
- Jangan mengganti data dinamis dengan data dummy.
- Jangan merusak responsivitas tampilan.
- Hindari duplikasi CSS dan komponen.
- Gunakan design tokens atau CSS variables agar warna dan styling konsisten.

## 8. Expected Result

Hasil akhir yang diharapkan:

Aplikasi memiliki tampilan profesional dengan identitas visual UMS yang kuat, dominasi biru tua, aksen kuning dan toska yang proporsional, typography yang konsisten, ikon minimalis, serta layout yang bersih.

Referensi pendekatan desain:
- Modern Enterprise Dashboard
- Minimalist Administrative Interface
- Clean SaaS Dashboard
- Professional University Branding

**PENTING:** Jangan mengubah desain menjadi terlalu polos atau kehilangan karakter. Tujuannya adalah menciptakan UI yang sederhana tetapi tetap memiliki visual hierarchy, karakter, dan kesan profesional.

Lakukan implementasi langsung pada source code project, bukan hanya memberikan rekomendasi atau mockup. Setelah selesai, periksa kembali seluruh halaman dan pastikan tidak ada fitur yang rusak.