<?php

namespace Database\Seeders;

use App\Models\Calon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CalonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->ensureSeedFotoExists('shabira-syahla-alvaliza.png');
        $this->ensureSeedFotoExists('faiz-nabil-akram.png');

        Calon::updateOrCreate(['nomor' => '01'], [
            'nama' => 'Shabira Syahla Alvaliza',
            'kelas' => 'XI-1',
            'foto' => 'shabira-syahla-alvaliza.png',
            'visi' => 'Menjadikan OSIS sebagai wadah yang inklusif, kreatif, dan berprestasi untuk seluruh siswa, serta menciptakan lingkungan sekolah yang harmonis dan berintegritas.',
            'misi' => <<<'MISI'
                1. Mengoptimalkan peran OSIS sebagai penghubung antara siswa dan sekolah.
                2. Mengembangkan program kerja yang kreatif dan bermanfaat bagi seluruh siswa.
                3. Meningkatkan kegiatan akademik dan non-akademik yang mendukung prestasi siswa.
                4. Mempererat kekeluargaan dan solidaritas antar siswa melalui kegiatan positif.
                5. Menjadi aspirasi siswa dan menjunjung tinggi transparansi dalam setiap kegiatan.
                MISI,
        ]);

        Calon::updateOrCreate(['nomor' => '02'], [
            'nama' => 'Faiz Nabil Akram',
            'kelas' => 'XII-1',
            'foto' => 'faiz-nabil-akram.png',
            'visi' => 'Mewujudkan OSIS yang profesional, berwawasan lingkungan, dan berorientasi pada pengembangan karakter siswa untuk menyongsong generasi emas Indonesia.',
            'misi' => <<<'MISI'
                1. Membangun OSIS yang profesional dengan sistem manajemen yang terstruktur dan akuntabel.
                2. Menggalakkan program peduli lingkungan seperti go green dan pengelolaan sampah.
                3. Menyelenggarakan pelatihan kepemimpinan dan soft skill bagi seluruh siswa.
                4. Memperbanyak kegiatan sosial dan bakti masyarakat untuk menumbuhkan kepedulian sosial.
                5. Mendorong siswa berprestasi melalui kompetisi dan pembinaan yang berkelanjutan.
                MISI,
        ]);
    }

    private function ensureSeedFotoExists(string $filename): void
    {
        $source = database_path('seeders/assets/foto_calon/'.$filename);
        $target = 'foto_calon/'.$filename;

        if (! file_exists($source)) {
            $this->command?->warn("Seed asset tidak ditemukan: {$source}");

            return;
        }

        if (! Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, file_get_contents($source));
        }
    }
}
