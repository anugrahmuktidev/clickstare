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
        Schema::table('videos', function (\Illuminate\Database\Schema\Blueprint $table) {
            if (!Schema::hasColumn('videos', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable()->after('path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (\Illuminate\Database\Schema\Blueprint $table) {
            if (Schema::hasColumn('videos', 'thumbnail_path')) {
                $table->dropColumn('thumbnail_path');
            }
        });
    }
};
