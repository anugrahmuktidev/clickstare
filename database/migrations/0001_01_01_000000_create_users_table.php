<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // dipakai untuk login (NIP / NISN)
            $table->string('username')->unique();

            // role aplikasi
            $table->enum('role', ['admin', 'guru', 'siswa'])->default('siswa');

            // nama utama (Laravel default)
            $table->string('name');

            // opsional email (tidak dipakai login)
            $table->string('email')->nullable();

            // password
            $table->string('password');

            // relasi sekolah (tanpa FK dulu agar tidak bentrok urutan migrasi)
            $table->unsignedBigInteger('sekolah_id')->nullable();

            // field khusus siswa
            $table->string('nisn')->nullable()->unique();
            $table->string('kelas')->nullable();
            $table->unsignedTinyInteger('umur')->nullable();

            // field khusus guru/admin
            $table->string('nip')->nullable()->unique();
            $table->string('jabatan')->nullable(); // wali kelas / guru / kepsek / BK

            // field default Laravel
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->timestamps();

            // indeks yang sering dipakai
            $table->index(['role', 'sekolah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
