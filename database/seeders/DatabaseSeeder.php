<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder: Pintu masuk utama untuk semua seeder.
 *
 * LOKASI FILE: database/seeders/DatabaseSeeder.php
 *
 * KENAPA ada file ini?
 * Saat kamu jalankan `php artisan db:seed`, Laravel hanya memanggil
 * file ini. Di sinilah kita atur URUTAN seeder dijalankan — urutan
 * ini penting karena ada relasi antar tabel (users harus ada dulu
 * sebelum polls bisa dibuat, polls harus ada sebelum candidates, dst).
 *
 * CARA PAKAI:
 *
 * Jalankan semua seeder (fresh — hapus semua data & isi ulang):
 *   php artisan migrate:fresh --seed
 *
 * Jalankan seeder tanpa hapus tabel (tambah data ke data yang ada):
 *   php artisan db:seed
 *
 * Jalankan satu seeder tertentu saja:
 *   php artisan db:seed --class=UserSeeder
 *
 * PERINGATAN `migrate:fresh --seed`:
 *   Perintah ini DROP semua tabel lalu buat ulang dari nol, kemudian
 *   langsung jalankan semua seeder. HANYA pakai saat development!
 *   Di production, ini akan menghapus semua data nyata.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan semua seeder dalam urutan yang benar.
     */
    public function run(): void
    {
        // URUTAN INI PENTING — jangan diubah sembarangan!
        // 1. User harus ada dulu (polls butuh created_by → users.id)
        // 2. Poll dibuat setelah user ada
        // 3. Candidates dan votes bergantung pada polls
        $this->call([
            UserSeeder::class,  // Buat admin + mahasiswa dummy
            PollSeeder::class,  // Buat polling aktif + kandidat dummy
        ]);

        $this->command->info('');
        $this->command->info('==============================================');
        $this->command->info(' Semua seeder selesai dijalankan!');
        $this->command->info('----------------------------------------------');
        $this->command->info(' Login sebagai ADMIN:');
        $this->command->info('   Email   : admin@kampus.ac.id');
        $this->command->info('   Password: admin123');
        $this->command->info('----------------------------------------------');
        $this->command->info(' Login sebagai MAHASISWA (salah satu):');
        $this->command->info('   Email   : budi@mahasiswa.ac.id');
        $this->command->info('   Password: password123');
        $this->command->info('==============================================');
    }
}
