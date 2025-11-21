<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attitude_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attitude_question_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('stage', ['pre', 'post'])->default('pre');
            $table->enum('value', ['STS', 'TS', 'S', 'SS']);
            $table->timestamps();

            $table->unique(['attitude_question_id', 'user_id', 'stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attitude_answers');
    }
};
