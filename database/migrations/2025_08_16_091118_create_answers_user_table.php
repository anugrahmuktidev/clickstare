<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('answers_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_user_id')->constrained('questions_user')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // guru/admin penjawab
            $table->text('jawaban');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('answers_user');
    }
};
