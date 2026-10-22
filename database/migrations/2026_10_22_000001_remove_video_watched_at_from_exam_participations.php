<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('exam_participations', 'video_watched_at')) {
            Schema::table('exam_participations', function (Blueprint $table) {
                $table->dropColumn('video_watched_at');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('exam_participations', 'video_watched_at')) {
            Schema::table('exam_participations', function (Blueprint $table) {
                $table->timestamp('video_watched_at')->nullable();
            });
        }
    }
};
