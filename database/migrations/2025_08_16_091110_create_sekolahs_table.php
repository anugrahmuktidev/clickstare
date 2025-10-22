<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('npsn')->nullable()->unique();
            $table->string('alamat')->nullable();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sekolah_id')->references('id')->on('sekolahs')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
