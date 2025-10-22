<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_is_validated_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_validated')->default(false)->after('role');
            $table->timestamp('validated_at')->nullable()->after('is_validated');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_validated', 'validated_at']);
        });
    }
};
