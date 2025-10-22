<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\ParticipantValidation;

class InitialSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. Sekolah contoh ===
        $sma1 = Sekolah::firstOrCreate(
            ['nama' => 'SMA Negeri 1 Contoh'],
            ['npsn' => '12345678', 'alamat' => 'Jl. Merdeka No. 1']
        );

        $smp1 = Sekolah::firstOrCreate(
            ['nama' => 'SMP Negeri 1 Contoh'],
            ['npsn' => '87654321', 'alamat' => 'Jl. Pendidikan No. 2']
        );

        // === 2. Admin ===
        User::firstOrCreate(
            ['username' => '1987654321'], // username = NIP
            [
                'nip' => '123321',
                'role' => 'admin',
                'name' => 'Admin Clicstare',
                'password' => Hash::make('qwe123qwe'), // ganti di produksi
                'sekolah_id' => $sma1->id,
            ]
        );

        // === 3. Guru ===
        User::firstOrCreate(
            ['username' => '197700112233'],
            [
                'nip' => '197700112233',
                'role' => 'guru',
                'name' => 'Bu Guru Contoh',
                'password' => Hash::make('password'),
                'sekolah_id' => $sma1->id,
                'jabatan' => 'guru',
            ]
        );

        // === 4. Siswa ===
        $siswa = User::firstOrCreate(
            ['username' => '9988776655'], // username = NISN
            [
                'nisn' => '9988776655',
                'role' => 'siswa',
                'name' => 'Adi Siswa',
                'password' => Hash::make('password'),
                'sekolah_id' => $smp1->id,
                'kelas' => '9A',
                'umur' => 14,
            ]
        );
    }
}
