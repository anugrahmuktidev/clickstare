<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureAttitudeAnswersStageUnique();
        $this->ensureExamParticipationColumns();
    }

    public function down(): void
    {
        $this->revertExamParticipationColumns();
        $this->revertAttitudeAnswersIndex();
    }

    protected function ensureAttitudeAnswersStageUnique(): void
    {
        if (! Schema::hasTable('attitude_answers')) {
            return;
        }

        Schema::table('attitude_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('attitude_answers', 'stage')) {
                $table->enum('stage', ['pre', 'post'])->default('pre')->after('user_id');
            }
        });

        if (Schema::hasColumn('attitude_answers', 'stage')) {
            DB::table('attitude_answers')
                ->whereNull('stage')
                ->update(['stage' => 'pre']);
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        $database = DB::getDatabaseName();

        $this->dropForeignKeyIfExists('attitude_answers', 'attitude_answers_attitude_question_id_foreign');
        $this->dropForeignKeyIfExists('attitude_answers', 'attitude_answers_user_id_foreign');
        $this->dropIndexIfExists('attitude_answers', 'attitude_answers_attitude_question_id_user_id_unique');
        $this->dropIndexIfExists('attitude_answers', 'attitude_answers_question_user_stage_unique');

        DB::statement("ALTER TABLE `attitude_answers`
            ADD UNIQUE `attitude_answers_question_user_stage_unique` (`attitude_question_id`,`user_id`,`stage`)");

        DB::statement("ALTER TABLE `attitude_answers`
            ADD CONSTRAINT `attitude_answers_attitude_question_id_foreign`
            FOREIGN KEY (`attitude_question_id`) REFERENCES `attitude_questions`(`id`) ON DELETE CASCADE");

        DB::statement("ALTER TABLE `attitude_answers`
            ADD CONSTRAINT `attitude_answers_user_id_foreign`
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE");
    }

    protected function ensureExamParticipationColumns(): void
    {
        if (! Schema::hasTable('exam_participations')) {
            return;
        }

        Schema::table('exam_participations', function (Blueprint $table) {
            if (! Schema::hasColumn('exam_participations', 'sikap_completed_at')) {
                $table->timestamp('sikap_completed_at')->nullable()->after('pretest_completed_at');
            }
            if (! Schema::hasColumn('exam_participations', 'sikap_post_completed_at')) {
                $table->timestamp('sikap_post_completed_at')->nullable()->after('posttest_completed_at');
            }
        });
    }

    protected function revertExamParticipationColumns(): void
    {
        if (! Schema::hasTable('exam_participations')) {
            return;
        }

        Schema::table('exam_participations', function (Blueprint $table) {
            if (Schema::hasColumn('exam_participations', 'sikap_post_completed_at')) {
                $table->dropColumn('sikap_post_completed_at');
            }
            if (Schema::hasColumn('exam_participations', 'sikap_completed_at')) {
                $table->dropColumn('sikap_completed_at');
            }
        });
    }

    protected function revertAttitudeAnswersIndex(): void
    {
        if (! Schema::hasTable('attitude_answers')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        $this->dropForeignKeyIfExists('attitude_answers', 'attitude_answers_attitude_question_id_foreign');
        $this->dropForeignKeyIfExists('attitude_answers', 'attitude_answers_user_id_foreign');
        $this->dropIndexIfExists('attitude_answers', 'attitude_answers_question_user_stage_unique');

        DB::statement("ALTER TABLE `attitude_answers`
            ADD UNIQUE `attitude_answers_attitude_question_id_user_id_unique` (`attitude_question_id`,`user_id`)");

        DB::statement("ALTER TABLE `attitude_answers`
            ADD CONSTRAINT `attitude_answers_attitude_question_id_foreign`
            FOREIGN KEY (`attitude_question_id`) REFERENCES `attitude_questions`(`id`) ON DELETE CASCADE");

        DB::statement("ALTER TABLE `attitude_answers`
            ADD CONSTRAINT `attitude_answers_user_id_foreign`
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE");
    }

    protected function dropForeignKeyIfExists(string $table, string $constraint): void
    {
        $database = DB::getDatabaseName();
        $exists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $database)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
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
};
