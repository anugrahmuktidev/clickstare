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
        $this->ensureKnowledgeAnswersSupportStage();
        $this->ensureExamParticipationSupportPostKnowledgeStep();
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
                ->where('current_step', 'pengetahuan_test_post')
                ->update(['current_step' => 'done']);

            DB::statement("ALTER TABLE `exam_participations` MODIFY `current_step` ENUM('pretest','sikap','pengetahuan_test','video','posttest','sikap_post','done') NOT NULL DEFAULT 'pretest'");
        }

        Schema::table('exam_participations', function (Blueprint $table) {
            if (Schema::hasColumn('exam_participations', 'knowledge_test_post_completed_at')) {
                $table->dropColumn('knowledge_test_post_completed_at');
            }
        });
    }

    protected function ensureKnowledgeAnswersSupportStage(): void
    {
        if (! Schema::hasTable('knowledge_answers')) {
            return;
        }

        Schema::table('knowledge_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('knowledge_answers', 'stage')) {
                $table->enum('stage', ['pre', 'post'])->default('pre')->after('user_id');
            }
        });

        if (Schema::hasColumn('knowledge_answers', 'stage')) {
            DB::table('knowledge_answers')
                ->whereNull('stage')
                ->update(['stage' => 'pre']);
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $this->ensureSingleColumnIndex('knowledge_answers', 'knowledge_answers_knowledge_question_id_index', 'knowledge_question_id');

        $this->dropIndexIfExists('knowledge_answers', 'knowledge_answers_knowledge_question_id_user_id_unique');
        $this->dropIndexIfExists('knowledge_answers', 'knowledge_answers_question_user_stage_unique');

        DB::statement("ALTER TABLE `knowledge_answers`
            ADD UNIQUE `knowledge_answers_question_user_stage_unique` (`knowledge_question_id`,`user_id`,`stage`)");
    }

    protected function ensureExamParticipationSupportPostKnowledgeStep(): void
    {
        if (! Schema::hasTable('exam_participations')) {
            return;
        }

        $hasSikapPostCompletedAt = Schema::hasColumn('exam_participations', 'sikap_post_completed_at');

        Schema::table('exam_participations', function (Blueprint $table) use ($hasSikapPostCompletedAt) {
            if (! Schema::hasColumn('exam_participations', 'knowledge_test_post_completed_at')) {
                $column = $table->timestamp('knowledge_test_post_completed_at')->nullable();
                if ($hasSikapPostCompletedAt) {
                    $column->after('sikap_post_completed_at');
                }
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `exam_participations` MODIFY `current_step` ENUM('pretest','sikap','pengetahuan_test','video','posttest','sikap_post','pengetahuan_test_post','done') NOT NULL DEFAULT 'pretest'");
        }
    }

    protected function dropIndexIfExists(string $table, string $index): void
    {
        $database = DB::getDatabaseName();
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }

    protected function ensureSingleColumnIndex(string $table, string $index, string $column): void
    {
        $database = DB::getDatabaseName();
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();

        if (! $exists) {
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$index}`(`{$column}`)");
        }
    }
};
