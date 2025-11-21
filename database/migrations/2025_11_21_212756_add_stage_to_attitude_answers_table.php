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
        if (Schema::hasColumn('attitude_answers', 'stage')) {
            return;
        }

        Schema::table('attitude_answers', function (Blueprint $table) {
            $table->dropForeign('attitude_answers_attitude_question_id_foreign');
            $table->dropForeign('attitude_answers_user_id_foreign');
            $table->dropUnique('attitude_answers_attitude_question_id_user_id_unique');
            $table->enum('stage', ['pre', 'post'])->default('pre')->after('user_id');
        });

        DB::table('attitude_answers')
            ->whereNull('stage')
            ->orWhere('stage', '')
            ->update(['stage' => 'pre']);

        Schema::table('attitude_answers', function (Blueprint $table) {
            $table->unique(
                ['attitude_question_id', 'user_id', 'stage'],
                'attitude_answers_question_user_stage_unique'
            );

            $table->foreign('attitude_question_id')
                ->references('id')
                ->on('attitude_questions')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('attitude_answers', 'stage')) {
            return;
        }

        Schema::table('attitude_answers', function (Blueprint $table) {
            $table->dropForeign('attitude_answers_attitude_question_id_foreign');
            $table->dropForeign('attitude_answers_user_id_foreign');
            $table->dropUnique('attitude_answers_question_user_stage_unique');
            $table->unique(
                ['attitude_question_id', 'user_id'],
                'attitude_answers_attitude_question_id_user_id_unique'
            );
            $table->dropColumn('stage');

            $table->foreign('attitude_question_id')
                ->references('id')
                ->on('attitude_questions')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
