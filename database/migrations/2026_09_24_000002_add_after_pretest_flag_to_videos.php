<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (! Schema::hasColumn('videos', 'is_after_pretest')) {
                $table->boolean('is_after_pretest')
                    ->default(false)
                    ->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'is_after_pretest')) {
                $table->dropColumn('is_after_pretest');
            }
        });
    }
};
