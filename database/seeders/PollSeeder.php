<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Poll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * PollSeeder: Mengisi tabel polls dan candidates dengan data dummy untuk testing.
 *
 * LOKASI FILE: database/seeders/PollSeeder.php
 *
 * Isi yang akan dibuat:
 * - 1 polling berstatus 'aktif' (mulai hari ini, berakhir 7 hari ke depan)
 * - 3 kandidat dummy dengan visi-misi contoh
 *
 * CATATAN: Seeder ini harus dijalankan SETELAH UserSeeder, karena
 * kita butuh user admin (created_by) yang sudah ada di database.
 * Urutan ini diatur di DatabaseSeeder.php.
 */
class PollSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        // =====================================================================
        // Ambil user admin yang sudah dibuat oleh UserSeeder
        // =====================================================================
        // Kenapa pakai firstOrFail()? Agar jika admin belum ada (UserSeeder
        // belum jalan), langsung muncul error yang jelas, bukan error misterius
        // foreign key constraint.
        $admin = User::where('role', 'admin')->firstOrFail();

        // =====================================================================
        // 1 POLLING AKTIF — Pemilihan Ketua BEM 2026
        // =====================================================================
        $poll = Poll::create([
            'judul'       => 'Pemilihan Ketua BEM 2026',
            'deskripsi'   => 'Pemilihan Ketua dan Wakil Ketua Badan Eksekutif Mahasiswa (BEM) ' .
                             'Universitas Kampus Maju periode 2026/2027. ' .
                             'Setiap mahasiswa aktif berhak memberikan satu suara.',
            'status'      => 'aktif',

            // Mulai sekarang (hari ini), berakhir 7 hari ke depan
            // Carbon::now() → waktu saat seeder dijalankan
            // addDays(7)    → tambah 7 hari dari sekarang
            'mulai_pada'  => Carbon::now(),
            'selesai_pada' => Carbon::now()->addDays(7),

            'created_by'  => $admin->id,
        ]);

        $this->command->info("✓ Polling dibuat: \"{$poll->judul}\" (ID: {$poll->id})");

        // =====================================================================
        // 3 KANDIDAT DUMMY untuk polling di atas
        // =====================================================================
        $kandidat = [
            [
                'poll_id'       => $poll->id,
                'nomor_urut'    => 1,
                'nama_kandidat' => 'Andi Wijaya',
                'visi_misi'     => "VISI:\n" .
                    "Mewujudkan BEM yang transparan, inovatif, dan berpihak pada seluruh mahasiswa.\n\n" .
                    "MISI:\n" .
                    "1. Meningkatkan keterbukaan informasi dan akuntabilitas BEM kepada seluruh mahasiswa.\n" .
                    "2. Mengadakan program pengembangan soft skill dan hard skill mahasiswa setiap semester.\n" .
                    "3. Memperkuat hubungan BEM dengan pihak kampus dan komunitas eksternal untuk peluang magang dan beasiswa.\n" .
                    "4. Menyediakan platform aspirasi mahasiswa yang responsif dan terstruktur.",
                'foto'          => null, // foto kosong, akan pakai placeholder otomatis
            ],
            [
                'poll_id'       => $poll->id,
                'nomor_urut'    => 2,
                'nama_kandidat' => 'Bela Safitri',
                'visi_misi'     => "VISI:\n" .
                    "Membangun BEM yang solid, inklusif, dan mampu menjadi jembatan antara mahasiswa dan birokrasi kampus.\n\n" .
                    "MISI:\n" .
                    "1. Membentuk divisi khusus untuk menampung dan menindaklanjuti aspirasi mahasiswa minoritas dan berkebutuhan khusus.\n" .
                    "2. Menyelenggarakan forum diskusi bulanan bersama dekan dan rektorat.\n" .
                    "3. Merancang program kewirausahaan mahasiswa bertaraf nasional.\n" .
                    "4. Meningkatkan kualitas dan kuantitas kegiatan sosial kemasyarakatan BEM.",
                'foto'          => null,
            ],
            [
                'poll_id'       => $poll->id,
                'nomor_urut'    => 3,
                'nama_kandidat' => 'Cahyo Nugroho',
                'visi_misi'     => "VISI:\n" .
                    "BEM sebagai motor penggerak prestasi akademik dan non-akademik mahasiswa yang berdampak nyata.\n\n" .
                    "MISI:\n" .
                    "1. Mendirikan pusat belajar dan tutor sebaya gratis di setiap jurusan.\n" .
                    "2. Mengoptimalkan media sosial BEM sebagai sarana informasi beasiswa, kompetisi, dan lowongan kerja.\n" .
                    "3. Menjalin kerjasama dengan alumni kampus yang sudah berkiprah di industri.\n" .
                    "4. Membangun budaya organisasi yang sehat, anti-korupsi, dan berbasis kinerja terukur.",
                'foto'          => null,
            ],
        ];

        foreach ($kandidat as $data) {
            $c = Candidate::create($data);
            $this->command->info("  ✓ Kandidat No.{$c->nomor_urut}: {$c->nama_kandidat}");
        }

        $this->command->info('');
        $this->command->info('✓ PollSeeder selesai! Data siap untuk testing.');
    }
}
