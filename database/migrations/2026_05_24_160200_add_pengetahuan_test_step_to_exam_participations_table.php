<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('exam_participations')) {
            return;
        }

        $hasSikapCompletedAt = Schema::hasColumn('exam_participations', 'sikap_completed_at');

        Schema::table('exam_participations', function (Blueprint $table) use ($hasSikapCompletedAt) {
            if (! Schema::hasColumn('exam_participations', 'knowledge_test_completed_at')) {
                $column = $table->timestamp('knowledge_test_completed_at')->nullable();
                if ($hasSikapCompletedAt) {
                    $column->after('sikap_completed_at');
                }
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `exam_participations` MODIFY `current_step` ENUM('pretest','sikap','pengetahuan_test','video','posttest','sikap_post','done') NOT NULL DEFAULT 'pretest'");

            // Migrasi progres lama:
            // jika user sudah selesai sikap awal, tetapi belum menonton video/posttest,
            // anggap mereka harus melewati tahap pengetahuan_test dulu.
            DB::table('exam_participations')
                ->where('current_step', 'video')
                ->whereNotNull('sikap_completed_at')
                ->whereNull('knowledge_test_completed_at')
                ->whereNull('video_watched_at')
                ->whereNull('posttest_completed_at')
                ->whereNull('sikap_post_completed_at')
                ->update(['current_step' => 'pengetahuan_test']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('exam_participations')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('exam_participations')
                ->where('current_step', 'pengetahuan_test')
                ->update(['current_step' => 'video']);

            DB::statement("ALTER TABLE `exam_participations` MODIFY `current_step` ENUM('pretest','sikap','video','posttest','sikap_post','done') NOT NULL DEFAULT 'pretest'");
        }

        Schema::table('exam_participations', function (Blueprint $table) {
            if (Schema::hasColumn('exam_participations', 'knowledge_test_completed_at')) {
                $table->dropColumn('knowledge_test_completed_at');
            }
        });
    }
};
