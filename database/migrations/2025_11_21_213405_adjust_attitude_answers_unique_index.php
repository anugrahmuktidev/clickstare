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
        if (! Schema::hasTable('attitude_answers') || ! Schema::hasColumn('attitude_answers', 'stage')) {
            return;
        }

        if ($this->isMysql()) {
            $this->dropForeignIfExists('attitude_answers', 'attitude_answers_attitude_question_id_foreign');
            $this->dropForeignIfExists('attitude_answers', 'attitude_answers_user_id_foreign');
            $this->dropIndexIfExists('attitude_answers', 'attitude_answers_attitude_question_id_user_id_unique');

            DB::statement('ALTER TABLE `attitude_answers` ADD UNIQUE `attitude_answers_question_user_stage_unique` (`attitude_question_id`, `user_id`, `stage`)');
            DB::statement('ALTER TABLE `attitude_answers` ADD CONSTRAINT `attitude_answers_attitude_question_id_foreign` FOREIGN KEY (`attitude_question_id`) REFERENCES `attitude_questions`(`id`) ON DELETE CASCADE');
            DB::statement('ALTER TABLE `attitude_answers` ADD CONSTRAINT `attitude_answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
            return;
        }

        Schema::table('attitude_answers', function (Blueprint $table) {
            $table->dropUnique('attitude_answers_attitude_question_id_user_id_unique');
            $table->unique(['attitude_question_id', 'user_id', 'stage'], 'attitude_answers_question_user_stage_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('attitude_answers') || ! Schema::hasColumn('attitude_answers', 'stage')) {
            return;
        }

        if ($this->isMysql()) {
            $this->dropForeignIfExists('attitude_answers', 'attitude_answers_attitude_question_id_foreign');
            $this->dropForeignIfExists('attitude_answers', 'attitude_answers_user_id_foreign');
            $this->dropIndexIfExists('attitude_answers', 'attitude_answers_question_user_stage_unique');

            DB::statement('ALTER TABLE `attitude_answers` ADD UNIQUE `attitude_answers_attitude_question_id_user_id_unique` (`attitude_question_id`, `user_id`)');
            DB::statement('ALTER TABLE `attitude_answers` ADD CONSTRAINT `attitude_answers_attitude_question_id_foreign` FOREIGN KEY (`attitude_question_id`) REFERENCES `attitude_questions`(`id`) ON DELETE CASCADE');
            DB::statement('ALTER TABLE `attitude_answers` ADD CONSTRAINT `attitude_answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
            return;
        }

        Schema::table('attitude_answers', function (Blueprint $table) {
            $table->dropUnique('attitude_answers_question_user_stage_unique');
            $table->unique(['attitude_question_id', 'user_id'], 'attitude_answers_attitude_question_id_user_id_unique');
        });
    }
    protected function isMysql(): bool
    {
        return Schema::getConnection()->getDriverName() === 'mysql';
    }

    protected function dropForeignIfExists(string $table, string $constraint): void
    {
        $database = Schema::getConnection()->getDatabaseName();

        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }

    protected function dropIndexIfExists(string $table, string $index): void
    {
        $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);

        if (! empty($exists)) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }
};
