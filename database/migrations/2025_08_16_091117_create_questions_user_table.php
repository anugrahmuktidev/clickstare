<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // siswa penanya
            $table->text('pertanyaan');
            $table->enum('status', ['terkirim', 'dijawab', 'ditolak'])->default('terkirim');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('questions_user');
    }
};
