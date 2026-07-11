<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_questions', function (Blueprint $table) {
            $table->string('correct_answer', 10)
                ->default('BENAR')
                ->after('teks');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_questions', function (Blueprint $table) {
            $table->dropColumn('correct_answer');
        });
    }
};
