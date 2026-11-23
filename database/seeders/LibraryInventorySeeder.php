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
<<<<<<< HEAD
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
=======
        $catalog = [
            ['INV-38390', 'Mesin Laminating', 'Peralatan Kantor', 'Ruang Kepala Perpustakaan'],
            ['INV-20800', 'Mesin Penghancur Kertas', 'Peralatan Kantor', 'Ruang Referensi'],
            ['INV-99107', 'Kursi Kerja', 'Perabot', 'Ruang Baca'],
            ['INV-64501', 'Lemari Arsip', 'Perabot', 'Ruang Administrasi'],
            ['INV-70237', 'Kursi Kerja', 'Perabot', 'Gudang'],
            ['INV-80118', 'Rak Display Buku', 'Perlengkapan Perpustakaan', 'Ruang Pengolahan'],
            ['INV-40504', 'Troli Buku', 'Perlengkapan Perpustakaan', 'Ruang Sirkulasi'],
            ['INV-46335', 'Laptop', 'Elektronik', 'Ruang Referensi'],
            ['INV-32276', 'Kalkulator', 'Peralatan Kantor', 'Ruang Sirkulasi'],
            ['INV-10454', 'Vacuum Cleaner', 'Peralatan Kebersihan', 'Ruang Sirkulasi'],
            ['INV-44786', 'Stapler', 'Peralatan Kantor', 'Ruang Sirkulasi'],
            ['INV-64775', 'Printer', 'Elektronik', 'Gudang'],
            ['INV-94246', 'Proyektor', 'Elektronik', 'Ruang Pengolahan'],
            ['INV-82863', 'Mesin Penghancur Kertas', 'Peralatan Kantor', 'Gudang'],
            ['INV-77692', 'Stapler', 'Peralatan Kantor', 'Ruang Administrasi'],
            ['INV-42484', 'Komputer Desktop', 'Elektronik', 'Ruang Sirkulasi'],
            ['INV-11904', 'Vacuum Cleaner', 'Peralatan Kebersihan', 'Ruang Baca'],
            ['INV-43338', 'Mesin Penghancur Kertas', 'Peralatan Kantor', 'Ruang Kepala Perpustakaan'],
            ['INV-19350', 'Komputer Desktop', 'Elektronik', 'Gudang'],
            ['INV-26383', 'Kotak Pengembalian Buku', 'Perlengkapan Perpustakaan', 'Ruang Pengolahan'],
            ['INV-32663', 'Rak Buku', 'Perabot', 'Gudang'],
            ['INV-96377', 'Barcode Scanner', 'Perlengkapan Perpustakaan', 'Ruang Pengolahan'],
            ['INV-54350', 'Kalkulator', 'Peralatan Kantor', 'Ruang Kepala Perpustakaan'],
            ['INV-70603', 'Troli Buku', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-83682', 'Pel Lantai', 'Peralatan Kebersihan', 'Ruang Pengolahan'],
            ['INV-72604', 'Sapu Lantai', 'Peralatan Kebersihan', 'Ruang Baca'],
            ['INV-34856', 'Pel Lantai', 'Peralatan Kebersihan', 'Ruang Referensi'],
            ['INV-29691', 'Kotak Pengembalian Buku', 'Perlengkapan Perpustakaan', 'Gudang'],
            ['INV-11586', 'Proyektor', 'Elektronik', 'Ruang Kepala Perpustakaan'],
            ['INV-38029', 'Rak Display Buku', 'Perlengkapan Perpustakaan', 'Ruang Kepala Perpustakaan'],
            ['INV-12948', 'Mesin Laminating', 'Peralatan Kantor', 'Ruang Referensi'],
            ['INV-87664', 'Sapu Lantai', 'Peralatan Kebersihan', 'Ruang Administrasi'],
            ['INV-49843', 'Rak Display Buku', 'Perlengkapan Perpustakaan', 'Gudang'],
            ['INV-43153', 'Proyektor', 'Elektronik', 'Ruang Sirkulasi'],
            ['INV-89703', 'Kotak Pengembalian Buku', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-59935', 'Tempat Sampah', 'Peralatan Kebersihan', 'Ruang Sirkulasi'],
            ['INV-15565', 'Tempat Sampah', 'Peralatan Kebersihan', 'Ruang Sirkulasi'],
            ['INV-67300', 'Rak Display Buku', 'Perlengkapan Perpustakaan', 'Ruang Kepala Perpustakaan'],
            ['INV-29820', 'Rak Buku', 'Perabot', 'Ruang Referensi'],
            ['INV-53523', 'Kotak Pengembalian Buku', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-46749', 'Barcode Scanner', 'Perlengkapan Perpustakaan', 'Ruang Administrasi'],
            ['INV-25850', 'Sapu Lantai', 'Peralatan Kebersihan', 'Ruang Administrasi'],
            ['INV-74378', 'Komputer Desktop', 'Elektronik', 'Ruang Kepala Perpustakaan'],
            ['INV-36668', 'Rak Display Buku', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-18568', 'Laptop', 'Elektronik', 'Ruang Referensi'],
            ['INV-50359', 'Lemari Arsip', 'Perabot', 'Ruang Pengolahan'],
            ['INV-17497', 'Kalkulator', 'Peralatan Kantor', 'Ruang Pengolahan'],
            ['INV-71291', 'Lemari Arsip', 'Perabot', 'Ruang Referensi'],
            ['INV-25381', 'Mesin Laminating', 'Peralatan Kantor', 'Ruang Baca'],
            ['INV-80721', 'Laptop', 'Elektronik', 'Ruang Referensi'],
            ['INV-40469', 'Barcode Scanner', 'Perlengkapan Perpustakaan', 'Ruang Baca'],
            ['INV-29872', 'Rak Buku', 'Perabot', 'Ruang Referensi'],
            ['INV-22941', 'Meja Kerja', 'Perabot', 'Ruang Pengolahan'],
            ['INV-90274', 'Meja Kerja', 'Perabot', 'Ruang Referensi'],
            ['INV-22726', 'Pel Lantai', 'Peralatan Kebersihan', 'Ruang Administrasi'],
            ['INV-81122', 'Sapu Lantai', 'Peralatan Kebersihan', 'Gudang'],
            ['INV-11972', 'Rak Buku', 'Perabot', 'Ruang Pengolahan'],
            ['INV-16909', 'Proyektor', 'Elektronik', 'Gudang'],
            ['INV-39815', 'Meja Kerja', 'Perabot', 'Gudang'],
            ['INV-94999', 'Laptop', 'Elektronik', 'Ruang Pengolahan'],
            ['INV-17608', 'Kotak Pengembalian Buku', 'Perlengkapan Perpustakaan', 'Ruang Sirkulasi'],
            ['INV-70371', 'Vacuum Cleaner', 'Peralatan Kebersihan', 'Gudang'],
            ['INV-77810', 'Stapler', 'Peralatan Kantor', 'Ruang Sirkulasi'],
            ['INV-55054', 'Mesin Penghancur Kertas', 'Peralatan Kantor', 'Gudang'],
            ['INV-58297', 'Barcode Scanner', 'Perlengkapan Perpustakaan', 'Ruang Kepala Perpustakaan'],
            ['INV-58874', 'Sapu Lantai', 'Peralatan Kebersihan', 'Ruang Kepala Perpustakaan'],
            ['INV-73308', 'Stapler', 'Peralatan Kantor', 'Ruang Referensi'],
            ['INV-74112', 'Kalkulator', 'Peralatan Kantor', 'Ruang Baca'],
            ['INV-18231', 'Tempat Sampah', 'Peralatan Kebersihan', 'Ruang Referensi'],
            ['INV-56101', 'Vacuum Cleaner', 'Peralatan Kebersihan', 'Gudang'],
            ['INV-90058', 'Printer', 'Elektronik', 'Ruang Kepala Perpustakaan'],
            ['INV-49588', 'Kursi Kerja', 'Perabot', 'Gudang'],
            ['INV-72591', 'Troli Buku', 'Perlengkapan Perpustakaan', 'Ruang Administrasi'],
            ['INV-85245', 'Lemari Arsip', 'Perabot', 'Ruang Baca'],
            ['INV-21130', 'Kursi Kerja', 'Perabot', 'Ruang Sirkulasi'],
            ['INV-18198', 'Troli Buku', 'Perlengkapan Perpustakaan', 'Ruang Kepala Perpustakaan'],
            ['INV-20267', 'Troli Buku', 'Perlengkapan Perpustakaan', 'Ruang Referensi'],
            ['INV-90504', 'Pel Lantai', 'Peralatan Kebersihan', 'Ruang Kepala Perpustakaan'],
            ['INV-53169', 'Tempat Sampah', 'Peralatan Kebersihan', 'Ruang Kepala Perpustakaan'],
            ['INV-34930', 'Vacuum Cleaner', 'Peralatan Kebersihan', 'Ruang Sirkulasi'],
            ['INV-62809', 'Komputer Desktop', 'Elektronik', 'Ruang Sirkulasi'],
            ['INV-47324', 'Mesin Laminating', 'Peralatan Kantor', 'Ruang Baca'],
            ['INV-78712', 'Meja Kerja', 'Perabot', 'Ruang Sirkulasi'],
            ['INV-35742', 'Laptop', 'Elektronik', 'Ruang Administrasi'],
            ['INV-63823', 'Pel Lantai', 'Peralatan Kebersihan', 'Gudang'],
            ['INV-89956', 'Meja Kerja', 'Perabot', 'Gudang'],
            ['INV-14925', 'Kursi Kerja', 'Perabot', 'Ruang Kepala Perpustakaan'],
            ['INV-21986', 'Kalkulator', 'Peralatan Kantor', 'Ruang Referensi'],
            ['INV-95751', 'Printer', 'Elektronik', 'Ruang Administrasi'],
            ['INV-24983', 'Mesin Penghancur Kertas', 'Peralatan Kantor', 'Ruang Referensi'],
            ['INV-65733', 'Printer', 'Elektronik', 'Ruang Kepala Perpustakaan'],
            ['INV-67603', 'Proyektor', 'Elektronik', 'Ruang Referensi'],
            ['INV-43149', 'Komputer Desktop', 'Elektronik', 'Ruang Referensi'],
            ['INV-11662', 'Lemari Arsip', 'Perabot', 'Ruang Referensi'],
            ['INV-30575', 'Stapler', 'Peralatan Kantor', 'Ruang Kepala Perpustakaan'],
            ['INV-69844', 'Mesin Laminating', 'Peralatan Kantor', 'Ruang Pengolahan'],
            ['INV-80816', 'Rak Buku', 'Perabot', 'Ruang Administrasi'],
            ['INV-93566', 'Printer', 'Elektronik', 'Ruang Baca'],
            ['INV-31143', 'Barcode Scanner', 'Perlengkapan Perpustakaan', 'Ruang Referensi'],
            ['INV-60290', 'Tempat Sampah', 'Peralatan Kebersihan', 'Ruang Kepala Perpustakaan'],
        ];

        $categories = [
            'Elektronik',
            'Perabot',
            'Peralatan Kebersihan',
            'Peralatan Kantor',
            'Perlengkapan Perpustakaan',
        ];
        $locations = [
            'Gudang',
            'Ruang Administrasi',
            'Ruang Baca',
            'Ruang Kepala Perpustakaan',
            'Ruang Pengolahan',
            'Ruang Referensi',
            'Ruang Sirkulasi',
        ];

        $categoryIds = [];
        foreach ($categories as $categoryName) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => 'Kategori inventaris perpustakaan.']
            );
            $categoryIds[$categoryName] = $category->id;
        }

        $locationIds = [];
        foreach ($locations as $locationName) {
            $location = Location::updateOrCreate(
                ['name' => $locationName],
                ['description' => 'Lokasi inventaris perpustakaan.', 'is_active' => true]
            );
            $locationIds[$locationName] = $location->id;
        }

        $catalogCodes = array_column($catalog, 0);
        Item::whereNotIn('code', $catalogCodes)->update(['is_active' => false]);
        Location::whereNotIn('name', $locations)->update(['is_active' => false]);

        foreach ($catalog as [$code, $name, $categoryName, $locationName]) {
            Item::updateOrCreate(
                ['code' => $code],
                [
                    'category_id' => $categoryIds[$categoryName],
                    'location_id' => $locationIds[$locationName],
                    'name' => $name,
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
                    'quantity' => 1,
                    'unit' => 'Unit',
                    'condition' => 'Baik',
                    'status' => 'Tersedia',
                    'is_active' => true,
                    'purchase_price' => 0,
<<<<<<< HEAD
                    'description' => 'Data demo inventaris perpustakaan.',
                ]);
            }
        });
=======
                    'description' => 'Inventaris perpustakaan.',
                ]
            );
        }
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
    }
}
