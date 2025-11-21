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
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE `exam_participations`
            MODIFY `current_step`
            ENUM('pretest','sikap','video','posttest','sikap_post','done')
            NOT NULL DEFAULT 'pretest'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE `exam_participations`
            MODIFY `current_step`
            ENUM('pretest','sikap','video','posttest','done')
            NOT NULL DEFAULT 'pretest'
        ");
    }
};
