<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_participations', function (Blueprint $table) {
            $table->string('pocket_money_range', 50)->nullable()->after('current_step');
            $table->boolean('uses_electric_smoke')->nullable()->after('pocket_money_range');
            $table->boolean('uses_conventional_smoke')->nullable()->after('uses_electric_smoke');
            $table->boolean('uses_both_smoke_types')->nullable()->after('uses_conventional_smoke');
        });
    }

    public function down(): void
    {
        Schema::table('exam_participations', function (Blueprint $table) {
            $table->dropColumn([
                'pocket_money_range',
                'uses_electric_smoke',
                'uses_conventional_smoke',
                'uses_both_smoke_types',
            ]);
        });
    }
};
