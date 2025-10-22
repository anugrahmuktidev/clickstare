<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe', ['pre', 'post']);    // jenis attempt
            $table->unsignedInteger('total_soal');
            $table->unsignedInteger('total_benar');
            $table->unsignedInteger('score');        // 0..100
            $table->timestamps();

            $table->index(['user_id', 'tipe']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
    }
};
