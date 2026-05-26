<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->boolean('is_pretest_enabled')->default(false)->after('alamat');
            $table->boolean('is_posttest_enabled')->default(false)->after('is_pretest_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'is_pretest_enabled',
                'is_posttest_enabled',
            ]);
        });
    }
};
