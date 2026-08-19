<?php

namespace Database\Seeders;

use App\Models\Calon;
use App\Models\DisplayKey;
use App\Models\Vote;
use App\Models\Voter;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /**
     * Seed realistic demo data:
     * - 70 voters, jumlah yang sudah milih acak (~40–55)
     * - 4 display keys (bilik 1–4)
     * - votes disebar realistis dalam ~2 jam hari ini
     * - distribusi suara antar calon acak
     */
    public function run(): void
    {
        $allNames = [
            'Ahmad Sattar Maulana',
            'Aisyah Putri Ramadhani',
            'Budi Santoso',
            'Citra Dewi Lestari',
            'Dafa Rizky Pratama',
            'Dian Permatasari',
            'Eka Prasetyo Nugroho',
            'Farah Nabila Azzahra',
            'Gilang Ramadhan',
            'Hana Fitriani Sari',
            'Ibnu Hajar Al-Farisi',
            'Indah Kurniawati',
            'Joko Widodo Pratama',
            'Kartika Sari Dewi',
            'Luthfi Hakim Santoso',
            'Maya Safitri',
            'Naufal Arya Wibowo',
            'Nisa Aulia Rahma',
            'Octa Rizaldi',
            'Putri Handayani',
            'Rafi Ahmad Maulana',
            'Rahma Yulia Sari',
            'Rizky Firmansyah',
            'Salsabila Nur Azizah',
            'Taufik Hidayatullah',
            'Tiara Permata Indah',
            'Umar Faruq',
            'Vina Oktaviani',
            'Wahyu Setiawan',
            'Wulandari Kusuma',
            'Yoga Prakoso',
            'Yuli Astuti',
            'Zainal Abidin',
            'Zahra Fitria Rahayu',
            'Alif Bayu Setiawan',
            'Amira Dwi Lestari',
            'Bagas Dwi Saputro',
            'Bella Oktafia',
            'Chandra Maulana',
            'Desy Ratnasari',
            'Elsa Fitriani',
            'Fajar Nugroho',
            'Gita Cahyani',
            'Hendra Kurniawan',
            'Intan Permatasari',
            'Joni Prasetya',
            'Kiki Amalia',
            'Lutfi Anwar',
            'Mega Wulandari',
            'Nanda Putra Wijaya',
            'Ophi Saraswati',
            'Pandu Eka Nugraha',
            'Qori Auliannisa',
            'Rendra Bagaskara',
            'Sari Indah Lestari',
            'Teguh Prasetyo',
            'Ulfa Nurhidayah',
            'Vicky Ramadhan',
            'Windu Saputra',
            'Xena Aurelia',
            // 10 tambahan untuk total 70
            'Arya Bima Sakti',
            'Bunga Citra Melati',
            'Dimas Eka Putra',
            'Erina Safira Dewi',
            'Fauzan Habibi',
            'Gina Permata Sari',
            'Hafiz Maulana Yusuf',
            'Imelda Rosalinda',
            'Junaidi Pratama',
            'Keisha Ananda Putri',
        ];

        // Bersihkan data lama (urutan: votes dulu karena FK)
        DB::table('votes')->delete();
        DB::table('voters')->delete();
        DB::table('display_keys')->delete();

        // Ambil calon (harus sudah ada dari CalonSeeder)
        $calons = Calon::orderBy('nomor')->get();
        if ($calons->count() < 2) {
            $this->command->warn('CalonSeeder belum dijalankan. Jalankan php artisan db:seed --class=CalonSeeder dulu.');

            return;
        }

        $calon1 = $calons[0];
        $calon2 = $calons[1];

        // Buat 4 display keys (bilik voting)
        $displayKeys = [];
        foreach (['Bilik 1' => 'BILIK001', 'Bilik 2' => 'BILIK002', 'Bilik 3' => 'BILIK003', 'Bilik 4' => 'BILIK004'] as $nama => $key) {
            $displayKeys[] = DisplayKey::create([
                'nama' => $nama,
                'key' => $key,
                'successful_votes' => 0,
                'failed_attempts' => 0,
                'is_active' => true,
            ]);
        }

        // Buat semua 70 voters (semua belum milih dulu)
        $voters = [];
        foreach ($allNames as $name) {
            $voters[] = Voter::create([
                'nama' => $name,
                'has_voted' => false,
            ]);
        }

        // Tentukan berapa yang milih: random antara 40–55
        $totalVoted = rand(40, 55);

        // Acak urutan voters, ambil sebanyak $totalVoted
        $shuffledVoters = $voters;
        shuffle($shuffledVoters);
        $votingVoters = array_slice($shuffledVoters, 0, $totalVoted);

        // Distribusi suara acak: calon1 dapat antara 40–65% dari total
        $votes1Count = (int) round($totalVoted * (rand(40, 65) / 100));
        $votes2Count = $totalVoted - $votes1Count;

        $voteAssignments = array_merge(
            array_fill(0, $votes1Count, $calon1->id),
            array_fill(0, $votes2Count, $calon2->id),
        );
        shuffle($voteAssignments);

        // Waktu mulai voting: 2 jam yang lalu
        $startTime = Carbon::now()->subHours(2);
        $currentTime = $startTime->copy();

        // IP address dari jaringan sekolah
        $ips = ['192.168.1.10', '192.168.1.11', '192.168.1.12', '192.168.1.13'];

        foreach ($votingVoters as $i => $voter) {
            $calon_id = $voteAssignments[$i];

            // Jeda realistis dengan pola gelombang
            $minutesElapsed = $currentTime->diffInMinutes($startTime);
            if ($minutesElapsed < 30) {
                $gap = rand(1, 4);   // ramai di awal
            } elseif ($minutesElapsed < 90) {
                $gap = rand(2, 8);   // sepi di tengah
            } else {
                $gap = rand(1, 3);   // ramai lagi di akhir
            }

            $currentTime->addMinutes($gap)->addSeconds(rand(0, 59));

            if ($currentTime->greaterThan(Carbon::now())) {
                $currentTime = Carbon::now()->subSeconds(rand(30, 300));
            }

            // Round-robin antar bilik
            $displayKey = $displayKeys[$i % count($displayKeys)];

            Vote::create([
                'voter_id' => $voter->id,
                'calon_id' => $calon_id,
                'display_key_id' => $displayKey->id,
                'ip_address' => $ips[$i % count($ips)],
                'created_at' => $currentTime->copy(),
                'updated_at' => $currentTime->copy(),
            ]);

            $voter->update([
                'has_voted' => true,
                'updated_at' => $currentTime->copy(),
            ]);

            $displayKey->increment('successful_votes');
        }

        // Failed attempts realistis per bilik
        foreach ($displayKeys as $dk) {
            $dk->update(['failed_attempts' => rand(0, 5)]);
        }

        // Summary
        $voted = Voter::where('has_voted', true)->count();
        $notVoted = Voter::where('has_voted', false)->count();
        $totalVotes = Vote::count();
        $v1 = Vote::where('calon_id', $calon1->id)->count();
        $v2 = Vote::where('calon_id', $calon2->id)->count();

        $this->command->info('Demo seeding selesai:');
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total voters', Voter::count()],
                ['Sudah memilih', $voted],
                ['Belum memilih', $notVoted],
                ['Total votes', $totalVotes],
                ["{$calon1->nama}", "{$v1} suara"],
                ["{$calon2->nama}", "{$v2} suara"],
                ['Display keys', DisplayKey::count()],
            ]
        );
    }
}
