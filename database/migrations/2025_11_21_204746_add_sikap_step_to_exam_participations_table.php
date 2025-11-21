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
        Schema::table('exam_participations', function (Blueprint $table) {
            $table->timestamp('sikap_completed_at')->nullable()->after('pretest_completed_at');
            $table->timestamp('sikap_post_completed_at')->nullable()->after('posttest_completed_at');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `exam_participations` MODIFY `current_step` ENUM('pretest','sikap','video','posttest','sikap_post','done') NOT NULL DEFAULT 'pretest'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `exam_participations` MODIFY `current_step` ENUM('pretest','video','posttest','done') NOT NULL DEFAULT 'pretest'");
        }

        Schema::table('exam_participations', function (Blueprint $table) {
            $table->dropColumn('sikap_post_completed_at');
            $table->dropColumn('sikap_completed_at');
        });
    }
};
