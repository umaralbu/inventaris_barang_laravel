<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LibraryInventorySeeder extends Seeder
{
    public function run(): void
    {
        $categoryDescriptions = [
            'Peralatan Kantor' => 'Peralatan administrasi dan operasional kantor.',
            'Perabot' => 'Meja, kursi, dan perabot perpustakaan.',
            'Perlengkapan Perpustakaan' => 'Perlengkapan layanan dan pengelolaan perpustakaan.',
            'Elektronik' => 'Perangkat elektronik dan teknologi.',
            'Peralatan Kebersihan' => 'Peralatan kebersihan ruangan.',
        ];

        $locationNames = [
            'Ruang Kepala Perpustakaan',
            'Ruang Referensi',
            'Ruang Baca',
            'Ruang Administrasi',
            'Gudang',
            'Ruang Penyimpanan',
            'Ruang Pengolahan',
            'Ruang Sirkulasi',
        ];

        $items = [
            ['INV-38390', 'Mesin Laminating', 'Fellowes', 'Peralatan Kantor', 'Ruang Kepala Perpustakaan'],
            ['INV-20800', 'Mesin Penghancur Kertas', 'Fellowes', 'Peralatan Kantor', 'Ruang Referensi'],
            ['INV-99107', 'Kursi Kerja', 'Chitose', 'Perabot', 'Ruang Baca'],
            ['INV-64501', 'Lemari Arsip', 'Brother', 'Perabot', 'Ruang Administrasi'],
            ['INV-70237', 'Kursi Kerja', 'Indachi', 'Perabot', 'Gudang'],
            ['INV-80118', 'Rak Display Buku', 'Olympic', 'Perlengkapan Perpustakaan', 'Ruang Pengolahan'],
            ['INV-40504', 'Troli Buku', 'Lion', 'Perlengkapan Perpustakaan', 'Ruang Sirkulasi'],
            ['INV-46335', 'Laptop', 'ASUS', 'Elektronik', 'Ruang Referensi'],
            ['INV-32276', 'Kalkulator', 'Casio', 'Peralatan Kantor', 'Ruang Sirkulasi'],
            ['INV-10454', 'Vacuum Cleaner', 'Electrolux', 'Peralatan Kebersihan', 'Ruang Sirkulasi'],
            ['INV-44786', 'Stapler', 'Max', 'Peralatan Kantor', 'Ruang Sirkulasi'],
            ['INV-64775', 'Printer', 'Epson', 'Elektronik', 'Gudang'],
            ['INV-94246', 'Proyektor', 'Epson', 'Elektronik', 'Ruang Pengolahan'],
            ['INV-82863', 'Mesin Penghancur Kertas', 'GBC', 'Peralatan Kantor', 'Gudang'],
            ['INV-77692', 'Stapler', 'Kenko', 'Peralatan Kantor', 'Ruang Administrasi'],
            ['INV-42484', 'Komputer Desktop', 'Dell', 'Elektronik', 'Ruang Sirkulasi'],
            ['INV-11904', 'Vacuum Cleaner', 'Philips', 'Peralatan Kebersihan', 'Ruang Baca'],
            ['INV-43338', 'Mesin Penghancur Kertas', 'Kozure', 'Peralatan Kantor', 'Ruang Kepala Perpustakaan'],
            ['INV-19350', 'Komputer Desktop', 'HP', 'Elektronik', 'Gudang'],
            ['INV-26383', 'Kotak Pengembalian Buku', 'Lion Star', 'Perlengkapan Perpustakaan', 'Ruang Pengolahan'],
            ['INV-32663', 'Rak Buku', 'Olympic', 'Perabot', 'Gudang'],
            ['INV-96377', 'Barcode Scanner', 'Zebra', 'Perlengkapan Perpustakaan', 'Ruang Pengolahan'],
            ['INV-54350', 'Kalkulator', 'Citizen', 'Peralatan Kantor', 'Ruang Kepala Perpustakaan'],
            ['INV-70603', 'Troli Buku', 'Brother', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-83682', 'Pel Lantai', 'Vileda', 'Peralatan Kebersihan', 'Ruang Pengolahan'],
            ['INV-72604', 'Sapu Lantai', 'Bagus', 'Peralatan Kebersihan', 'Ruang Baca'],
            ['INV-34856', 'Pel Lantai', 'Bagus', 'Peralatan Kebersihan', 'Ruang Referensi'],
            ['INV-29691', 'Kotak Pengembalian Buku', 'Napolly', 'Perlengkapan Perpustakaan', 'Gudang'],
            ['INV-11586', 'Proyektor', 'BenQ', 'Elektronik', 'Ruang Kepala Perpustakaan'],
            ['INV-38029', 'Rak Display Buku', 'Brother', 'Perlengkapan Perpustakaan', 'Ruang Kepala Perpustakaan'],
            ['INV-12948', 'Mesin Laminating', 'GBC', 'Peralatan Kantor', 'Ruang Referensi'],
            ['INV-87664', 'Sapu Lantai', 'Lion Star', 'Peralatan Kebersihan', 'Ruang Administrasi'],
            ['INV-89703', 'Kotak Pengembalian Buku', 'Claris', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-59935', 'Tempat Sampah', 'Lion Star', 'Peralatan Kebersihan', 'Ruang Sirkulasi'],
            ['INV-15565', 'Tempat Sampah', 'Napolly', 'Peralatan Kebersihan', 'Ruang Sirkulasi'],
            ['INV-29820', 'Rak Buku', 'Brother', 'Perabot', 'Ruang Referensi'],
            ['INV-46749', 'Barcode Scanner', 'Honeywell', 'Perlengkapan Perpustakaan', 'Ruang Administrasi'],
            ['INV-74378', 'Komputer Desktop', 'Lenovo', 'Elektronik', 'Ruang Kepala Perpustakaan'],
            ['INV-18568', 'Laptop', 'Lenovo', 'Elektronik', 'Ruang Referensi'],
            ['INV-50359', 'Lemari Arsip', 'Alba', 'Perabot', 'Ruang Pengolahan'],
            ['INV-71291', 'Lemari Arsip', 'Chitose', 'Perabot', 'Ruang Referensi'],
            ['INV-25381', 'Mesin Laminating', 'Ibico', 'Peralatan Kantor', 'Ruang Baca'],
            ['INV-80721', 'Laptop', 'HP', 'Elektronik', 'Ruang Referensi'],
            ['INV-40469', 'Barcode Scanner', 'Datalogic', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-29872', 'Rak Buku', 'Lion', 'Perabot', 'Ruang Referensi'],
            ['INV-22941', 'Meja Kerja', 'Chitose', 'Perabot', 'Ruang Pengolahan'],
            ['INV-90274', 'Meja Kerja', 'Olympic', 'Perabot', 'Ruang Referensi'],
            ['INV-22726', 'Pel Lantai', 'Kenmaster', 'Peralatan Kebersihan', 'Ruang Administrasi'],
            ['INV-70371', 'Vacuum Cleaner', 'Sharp', 'Peralatan Kebersihan', 'Gudang'],
            ['INV-90058', 'Printer', 'Canon', 'Elektronik', 'Ruang Kepala Perpustakaan'],
        ];

        DB::transaction(function () use ($categoryDescriptions, $locationNames, $items): void {
            $demoCodes = array_column($items, 0);

            if (Item::whereIn('code', $demoCodes)->whereHas('transactions')->exists()) {
                throw new RuntimeException('Kode demo tidak dapat digunakan ulang karena memiliki histori transaksi.');
            }

            Item::whereHas('transactions')->update(['is_active' => false]);
            Item::whereDoesntHave('transactions')->delete();
            Category::whereDoesntHave('items')->delete();
            Location::whereDoesntHave('items')->delete();
            Location::whereHas('items')->update(['is_active' => false]);

            $categoryIds = [];
            foreach ($categoryDescriptions as $name => $description) {
                $categoryIds[$name] = Category::updateOrCreate(
                    ['name' => $name],
                    ['description' => $description]
                )->id;
            }

            $locationIds = [];
            foreach ($locationNames as $name) {
                $locationIds[$name] = Location::updateOrCreate(
                    ['name' => $name],
                    [
                        'description' => 'Lokasi inventaris demo.',
                        'is_active' => true,
                    ]
                )->id;
            }

            foreach ($items as [$code, $name, $brand, $category, $location]) {
                Item::create([
                    'code' => $code,
                    'name' => $name,
                    'brand' => $brand,
                    'category_id' => $categoryIds[$category],
                    'location_id' => $locationIds[$location],
                    'quantity' => 1,
                    'unit' => 'Unit',
                    'condition' => 'Baik',
                    'status' => 'Tersedia',
                    'is_active' => true,
                    'purchase_price' => 0,
                    'description' => 'Data demo inventaris perpustakaan.',
                ]);
            }
        });
    }
}