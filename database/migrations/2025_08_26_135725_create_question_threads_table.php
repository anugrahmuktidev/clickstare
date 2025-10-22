<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_threads', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();           // penanya (siswa)
            $t->foreignId('sekolah_id')->nullable()->constrained()->nullOnDelete();
            $t->string('judul');
            $t->text('isi');
            $t->enum('status', ['open', 'closed'])->default('open');
            $t->timestamp('solved_at')->nullable();
            $t->timestamps(); // wajib agar created_at / updated_at ada
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_threads');
    }
};
