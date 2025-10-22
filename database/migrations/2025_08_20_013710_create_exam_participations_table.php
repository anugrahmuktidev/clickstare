<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_participations', function (Blueprint $table) {
            $table->id();

            // relasi ke users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();


            // step progres
            $table->enum('current_step', ['pretest', 'video', 'posttest', 'done'])->default('pretest');

            // timestamp penyelesaian tiap langkah (opsional, berguna untuk audit)
            $table->timestamp('pretest_completed_at')->nullable();
            $table->timestamp('video_watched_at')->nullable();
            $table->timestamp('posttest_completed_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id']); // satu user satu exam satu baris progres
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_participations');
    }
};
