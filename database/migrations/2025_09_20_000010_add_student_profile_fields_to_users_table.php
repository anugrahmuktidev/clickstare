<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jenis_kelamin', 20)->nullable()->after('kelas');
            $table->string('pekerjaan_orangtua', 120)->nullable()->after('jenis_kelamin');
            $table->text('alamat')->nullable()->after('pekerjaan_orangtua');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'pekerjaan_orangtua', 'alamat']);
        });
    }
};
