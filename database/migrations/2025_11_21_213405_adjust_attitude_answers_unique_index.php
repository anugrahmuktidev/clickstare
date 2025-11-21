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

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `attitude_answers` DROP FOREIGN KEY `attitude_answers_attitude_question_id_foreign`');
            DB::statement('ALTER TABLE `attitude_answers` DROP FOREIGN KEY `attitude_answers_user_id_foreign`');
            DB::statement('ALTER TABLE `attitude_answers` DROP INDEX `attitude_answers_attitude_question_id_user_id_unique`');
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

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `attitude_answers` DROP FOREIGN KEY `attitude_answers_attitude_question_id_foreign`');
            DB::statement('ALTER TABLE `attitude_answers` DROP FOREIGN KEY `attitude_answers_user_id_foreign`');
            DB::statement('ALTER TABLE `attitude_answers` DROP INDEX `attitude_answers_question_user_stage_unique`');
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
};
