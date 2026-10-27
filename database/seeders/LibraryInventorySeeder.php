<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Database\Seeder;

class LibraryInventorySeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'code' => 'ADM',
                'name' => 'Ruang Administrasi',
                'description' => 'Ruang kerja administrasi perpustakaan.',
                'items' => [
                    ['Komputer Desktop', 'Unit'], ['Laptop', 'Unit'], ['Monitor', 'Unit'],
                    ['Printer', 'Unit'], ['Scanner', 'Unit'], ['Keyboard', 'Unit'],
                    ['Mouse', 'Unit'], ['Meja Kerja', 'Unit'], ['Kursi Kerja', 'Unit'],
                    ['Lemari Arsip', 'Unit'], ['Telepon Kantor', 'Unit'], ['Kalkulator', 'Unit'],
                    ['Stapler', 'Unit'], ['Mesin Penghancur Kertas', 'Unit'], ['UPS', 'Unit'],
                ],
            ],
            [
                'code' => 'STF',
                'name' => 'Ruang Kerja Staf',
                'description' => 'Ruang kerja staf perpustakaan.',
                'items' => [
                    ['Komputer Desktop', 'Unit'], ['Laptop', 'Unit'], ['Monitor', 'Unit'],
                    ['Keyboard', 'Unit'], ['Mouse', 'Unit'], ['Meja Kerja', 'Unit'],
                    ['Kursi Kerja', 'Unit'], ['Headset', 'Unit'], ['Lampu Meja', 'Unit'],
                    ['Kipas Angin', 'Unit'], ['Extension Cable', 'Unit'], ['Charger Laptop', 'Unit'],
                    ['Whiteboard', 'Unit'], ['Proyektor Mini', 'Unit'], ['Lemari Penyimpanan', 'Unit'],
                ],
            ],
            [
                'code' => 'GDG',
                'name' => 'Gudang',
                'description' => 'Tempat penyimpanan barang cadangan dan perlengkapan.',
                'items' => [
                    ['Laptop Cadangan', 'Unit'], ['Monitor Cadangan', 'Unit'], ['Keyboard', 'Unit'],
                    ['Mouse', 'Unit'], ['Printer Cadangan', 'Unit'], ['Kabel HDMI', 'Buah'],
                    ['Kabel LAN', 'Buah'], ['Kabel Power', 'Buah'], ['Charger Laptop', 'Unit'],
                    ['Proyektor', 'Unit'], ['Speaker', 'Unit'], ['Stop Kontak', 'Unit'],
                    ['Extension Cable', 'Unit'], ['Kursi Cadangan', 'Unit'], ['Meja Cadangan', 'Unit'],
                ],
            ],
            [
                'code' => 'RPT',
                'name' => 'Ruang Rapat',
                'description' => 'Ruang rapat dan kegiatan perpustakaan.',
                'items' => [
                    ['Meja Rapat', 'Unit'], ['Kursi Rapat', 'Unit'], ['Proyektor', 'Unit'],
                    ['Layar Proyektor', 'Unit'], ['Laptop', 'Unit'], ['Speaker', 'Unit'],
                    ['Microphone', 'Unit'], ['Remote Proyektor', 'Unit'], ['Kabel HDMI', 'Buah'],
                    ['Whiteboard', 'Unit'], ['AC', 'Unit'], ['Extension Cable', 'Unit'],
                    ['Webcam', 'Unit'], ['Televisi / Smart TV', 'Unit'], ['Pointer Presentasi', 'Unit'],
                ],
            ],
            [
                'code' => 'IT',
                'name' => 'Ruang Komputer / IT',
                'description' => 'Ruang komputer, server, dan perangkat teknologi informasi.',
                'items' => [
                    ['Komputer Desktop', 'Unit'], ['Monitor', 'Unit'], ['Laptop', 'Unit'],
                    ['Keyboard', 'Unit'], ['Mouse', 'Unit'], ['Printer', 'Unit'],
                    ['Scanner', 'Unit'], ['Router', 'Unit'], ['Switch Hub', 'Unit'],
                    ['Access Point', 'Unit'], ['Server', 'Unit'], ['UPS', 'Unit'],
                    ['Kabel LAN', 'Buah'], ['Hard Disk Eksternal', 'Unit'], ['Flashdisk', 'Unit'],
                ],
            ],
            [
                'code' => 'SRK',
                'name' => 'Ruang Layanan / Sirkulasi',
                'description' => 'Ruang layanan pemustaka dan sirkulasi koleksi.',
                'items' => [
                    ['Komputer Desktop', 'Unit'], ['Monitor', 'Unit'], ['Barcode Scanner', 'Unit'],
                    ['Printer', 'Unit'], ['Scanner Dokumen', 'Unit'], ['Meja Layanan', 'Unit'],
                    ['Kursi Petugas', 'Unit'], ['Telepon Kantor', 'Unit'], ['Mesin Absensi', 'Unit'],
                    ['CCTV', 'Unit'], ['Rak Buku', 'Unit'], ['Troli Buku', 'Unit'],
                    ['Label Printer', 'Unit'], ['Stempel', 'Buah'], ['Mesin Laminating', 'Unit'],
                ],
            ],
            [
                'code' => 'ARS',
                'name' => 'Ruang Arsip',
                'description' => 'Ruang penyimpanan arsip dan dokumen.',
                'items' => [
                    ['Lemari Arsip', 'Unit'], ['Rak Dokumen', 'Unit'], ['Komputer', 'Unit'],
                    ['Scanner Dokumen', 'Unit'], ['Printer', 'Unit'], ['Mesin Penghancur Kertas', 'Unit'],
                    ['Meja Kerja', 'Unit'], ['Kursi', 'Unit'], ['Filing Cabinet', 'Unit'], ['Brankas', 'Unit'],
                ],
            ],
            [
                'code' => 'IST',
                'name' => 'Ruang Istirahat Staf',
                'description' => 'Ruang istirahat staf perpustakaan.',
                'items' => [
                    ['Kulkas', 'Unit'], ['Dispenser', 'Unit'], ['Microwave', 'Unit'],
                    ['Meja', 'Unit'], ['Kursi', 'Unit'], ['Kipas Angin', 'Unit'],
                    ['Televisi', 'Unit'], ['Lemari', 'Unit'], ['Tempat Sampah', 'Unit'], ['Rak Piring', 'Unit'],
                ],
            ],
        ];

        $legacyCodes = array_map(
            fn (int $number) => 'INV-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT),
            range(1, 20)
        );

        Item::whereIn('code', $legacyCodes)->update(['is_active' => false]);
        Location::whereIn('name', ['Ruang Admin', 'Ruang Rapat', 'Ruang IT'])
            ->update(['is_active' => false]);

        $category = Category::firstOrCreate(
            ['name' => 'Perlengkapan Perpustakaan'],
            ['description' => 'Aset dan perlengkapan operasional perpustakaan.']
        );

        foreach ($locations as $locationData) {
            $location = Location::updateOrCreate(
                ['name' => $locationData['name']],
                [
                    'description' => $locationData['description'],
                    'is_active' => true,
                ]
            );

            foreach ($locationData['items'] as $index => [$name, $unit]) {
                $code = sprintf('INV-%s-%03d', $locationData['code'], $index + 1);

                Item::firstOrCreate(
                    ['code' => $code],
                    [
                        'category_id' => $category->id,
                        'location_id' => $location->id,
                        'name' => $name,
                        'quantity' => 1,
                        'unit' => $unit,
                        'condition' => 'Baik',
                        'status' => 'Tersedia',
                        'is_active' => true,
                        'purchase_price' => 0,
                        'description' => 'Inventaris perpustakaan di ' . $location->name,
                    ]
                );
            }
        }
    }
}