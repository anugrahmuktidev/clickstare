<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['pre', 'post']);
            $table->unsignedInteger('nomor');
            $table->text('teks');
            $table->timestamps();

            // ❌ JANGAN: $table->unique('nomor');
            // ✅ YANG BENAR: unik per (tipe, nomor)
            $table->unique(['tipe', 'nomor'], 'questions_tipe_nomor_unique');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
