<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_replies', function (Blueprint $t) {
            $t->id();
            $t->foreignId('thread_id')->constrained('question_threads')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();          // penjawab (admin/guru)
            $t->text('isi');
            $t->boolean('is_solution')->default(false);
            $t->timestamps(); // penting kalau mau sort by created_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_replies');
    }
};
