<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->isMysql()) {
            DB::statement("
                ALTER TABLE knowledge_answers
                MODIFY COLUMN value ENUM('STS', 'TS', 'S', 'SS', 'BENAR', 'SALAH') NOT NULL
            ");
        }

        DB::statement("
            UPDATE knowledge_answers
            SET value = CASE
                WHEN value IN ('S', 'SS') THEN 'BENAR'
                WHEN value IN ('TS', 'STS') THEN 'SALAH'
                ELSE value
            END
        ");

        if ($this->isMysql()) {
            DB::statement("
                ALTER TABLE knowledge_answers
                MODIFY COLUMN value ENUM('BENAR', 'SALAH') NOT NULL
            ");
        }
    }

    public function down(): void
    {
        if ($this->isMysql()) {
            DB::statement("
                ALTER TABLE knowledge_answers
                MODIFY COLUMN value ENUM('STS', 'TS', 'S', 'SS', 'BENAR', 'SALAH') NOT NULL
            ");
        }

        DB::statement("
            UPDATE knowledge_answers
            SET value = CASE
                WHEN value = 'BENAR' THEN 'S'
                WHEN value = 'SALAH' THEN 'TS'
                ELSE value
            END
        ");

        if ($this->isMysql()) {
            DB::statement("
                ALTER TABLE knowledge_answers
                MODIFY COLUMN value ENUM('STS', 'TS', 'S', 'SS') NOT NULL
            ");
        }
    }

    protected function isMysql(): bool
    {
        return DB::connection()->getDriverName() === 'mysql';
    }
};
