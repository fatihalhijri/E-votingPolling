<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder: Mengisi tabel users dengan data dummy untuk testing.
 *
 * LOKASI FILE: database/seeders/UserSeeder.php
 *
 * Isi yang akan dibuat:
 * - 1 user Admin (bisa membuat & mengelola polling)
 * - 5 user Mahasiswa (bisa vote di polling aktif)
 *
 * CARA MENJALANKAN (dari terminal, di folder evote-kampus):
 *   php artisan db:seed --class=UserSeeder
 *
 * Atau jalankan semua seeder sekaligus:
 *   php artisan db:seed
 *
 * Kalau mau RESET ulang data (hapus semua & isi ulang):
 *   php artisan migrate:fresh --seed
 *   → HATI-HATI: ini hapus SEMUA data termasuk yang asli!
 *     Hanya pakai saat development/testing, jangan di production.
 */
class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     *
     * KENAPA pakai Hash::make() bukan langsung string?
     * Password di database TIDAK BOLEH disimpan sebagai plain text.
     * Hash::make() menggunakan bcrypt (default Laravel) untuk mengamankan password.
     * Saat user login, Laravel otomatis bandingkan input dengan hash ini via Hash::check().
     *
     * CATATAN: Karena $casts = ['password' => 'hashed'] sudah ada di model User,
     * sebenarnya kita bisa langsung tulis 'password' => 'password123' dan Laravel
     * akan otomatis hash. Tapi kita pakai Hash::make() di sini agar lebih eksplisit
     * dan kamu benar-benar paham prosesnya.
     */
    public function run(): void
    {
        // =====================================================================
        // 1 USER ADMIN
        // =====================================================================
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@kampus.ac.id',
            'password' => Hash::make('admin123'),  // hash password, jangan plain text!
            'nim'      => null,                    // admin tidak punya NIM mahasiswa
            'role'     => 'admin',
            'prodi'    => null,
        ]);

        $this->command->info('✓ User admin dibuat: admin@kampus.ac.id / admin123');

        // =====================================================================
        // 5 USER MAHASISWA DUMMY
        // =====================================================================
        $mahasiswa = [
            [
                'name'     => 'Budi Santoso',
                'email'    => 'budi@mahasiswa.ac.id',
                'password' => Hash::make('password123'),
                'nim'      => '2024001001',
                'role'     => 'mahasiswa',
                'prodi'    => 'Teknik Informatika',
            ],
            [
                'name'     => 'Siti Rahayu',
                'email'    => 'siti@mahasiswa.ac.id',
                'password' => Hash::make('password123'),
                'nim'      => '2024001002',
                'role'     => 'mahasiswa',
                'prodi'    => 'Sistem Informasi',
            ],
            [
                'name'     => 'Ahmad Fauzi',
                'email'    => 'ahmad@mahasiswa.ac.id',
                'password' => Hash::make('password123'),
                'nim'      => '2024001003',
                'role'     => 'mahasiswa',
                'prodi'    => 'Teknik Informatika',
            ],
            [
                'name'     => 'Dewi Lestari',
                'email'    => 'dewi@mahasiswa.ac.id',
                'password' => Hash::make('password123'),
                'nim'      => '2024001004',
                'role'     => 'mahasiswa',
                'prodi'    => 'Manajemen Informatika',
            ],
            [
                'name'     => 'Rizky Pratama',
                'email'    => 'rizky@mahasiswa.ac.id',
                'password' => Hash::make('password123'),
                'nim'      => '2024001005',
                'role'     => 'mahasiswa',
                'prodi'    => 'Teknik Komputer',
            ],
        ];

        foreach ($mahasiswa as $data) {
            User::create($data);
        }

        $this->command->info('✓ 5 user mahasiswa dummy dibuat (password: password123)');
    }
}
