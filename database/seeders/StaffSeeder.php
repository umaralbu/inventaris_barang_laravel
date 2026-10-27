<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Maria Husnun Nisa, S.Sos., M.A.',
            'Hardika Dwi Hermawan, S.Pd., M.Sc',
            'Gatiningsih, SIP.',
            'Ken Retno Yuniwati, SIP.',
            'Ana Wahyuni, S.Sos.',
            'Cahyana Kumbul Widada, SIP',
            'Esti Handayani, S.I.Pust.',
            'Sapta Pujiyanta, S.I.Pust.',
            'Ari Fatmawati Aisyah, SIP.',
            'Mansur Hidayat, S.IIP.',
            'Nasyid Nur Hasan Fuad, S.I.Kom.',
            'Suansah, S.Pd.',
            'Mulyadi',
            'Munawar',
            'Hanun Adlan, SIP.',
            'Nizar Rahman, S.H., S.H.I.',
            'Qodri Royani, S.I.Pust.',
            'Muhammad Asharul Ma’ali, S.Kom.',
            'Aisha Rachmalia Rayhan, S.Hum.',
            'Ahmad Sugeng Riyadi',
            'Zahra Fadhilah Husna, S.Hum.',
            'Rohmadiyanto',
            'Jajar',
            'Bagus',
            'Ridwan',
            'Yuli',
            'Wiyono',
        ];

        foreach ($names as $name) {
            Staff::firstOrCreate(['name' => $name]);
        }
    }
}