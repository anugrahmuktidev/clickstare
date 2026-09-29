<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('school_classes')) {
            Schema::create('school_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
                $table->string('nama');
                $table->timestamps();

                $table->unique(['sekolah_id', 'nama']);
            });
        }

        if (! Schema::hasTable('exam_sessions')) {
            Schema::create('exam_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
                $table->string('nama');
                $table->boolean('is_active')->default(false);
                $table->timestamps();

                $table->unique(['sekolah_id', 'nama']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'school_class_id')) {
                $table->foreignId('school_class_id')
                    ->nullable()
                    ->after('sekolah_id')
                    ->constrained('school_classes')
                    ->nullOnDelete();
            }
        });

        Schema::table('exam_participations', function (Blueprint $table) {
            if (! Schema::hasColumn('exam_participations', 'exam_session_id')) {
                $table->foreignId('exam_session_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('exam_sessions')
                    ->nullOnDelete();
            }
        });

        Schema::table('knowledge_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('knowledge_answers', 'exam_session_id')) {
                $table->foreignId('exam_session_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('exam_sessions')
                    ->nullOnDelete();
            }
        });

        Schema::table('attitude_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('attitude_answers', 'exam_session_id')) {
                $table->foreignId('exam_session_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('exam_sessions')
                    ->nullOnDelete();
            }
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            if (! Schema::hasColumn('test_attempts', 'exam_session_id')) {
                $table->foreignId('exam_session_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('exam_sessions')
                    ->nullOnDelete();
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            if (! $this->indexExists('exam_participations', 'exam_participations_user_id_index')) {
                DB::statement('ALTER TABLE `exam_participations` ADD INDEX `exam_participations_user_id_index` (`user_id`)');
            }
            $this->dropIndexIfExists('exam_participations', 'exam_participations_user_id_unique');
            if (! $this->indexExists('exam_participations', 'exam_participations_user_session_unique')) {
                DB::statement('ALTER TABLE `exam_participations` ADD UNIQUE `exam_participations_user_session_unique` (`user_id`, `exam_session_id`)');
            }

            if (! $this->indexExists('knowledge_answers', 'knowledge_answers_knowledge_question_id_index')) {
                DB::statement('ALTER TABLE `knowledge_answers` ADD INDEX `knowledge_answers_knowledge_question_id_index` (`knowledge_question_id`)');
            }
            if (! $this->indexExists('knowledge_answers', 'knowledge_answers_user_id_index')) {
                DB::statement('ALTER TABLE `knowledge_answers` ADD INDEX `knowledge_answers_user_id_index` (`user_id`)');
            }
            $this->dropIndexIfExists('knowledge_answers', 'knowledge_answers_question_user_stage_unique');
            if (! $this->indexExists('knowledge_answers', 'knowledge_answers_question_user_session_stage_unique')) {
                DB::statement('ALTER TABLE `knowledge_answers` ADD UNIQUE `knowledge_answers_question_user_session_stage_unique` (`knowledge_question_id`, `user_id`, `exam_session_id`, `stage`)');
            }

            if (! $this->indexExists('attitude_answers', 'attitude_answers_attitude_question_id_index')) {
                DB::statement('ALTER TABLE `attitude_answers` ADD INDEX `attitude_answers_attitude_question_id_index` (`attitude_question_id`)');
            }
            if (! $this->indexExists('attitude_answers', 'attitude_answers_user_id_index')) {
                DB::statement('ALTER TABLE `attitude_answers` ADD INDEX `attitude_answers_user_id_index` (`user_id`)');
            }
            $this->dropIndexIfExists('attitude_answers', 'attitude_answers_question_user_stage_unique');
            if (! $this->indexExists('attitude_answers', 'attitude_answers_question_user_session_stage_unique')) {
                DB::statement('ALTER TABLE `attitude_answers` ADD UNIQUE `attitude_answers_question_user_session_stage_unique` (`attitude_question_id`, `user_id`, `exam_session_id`, `stage`)');
            }

            if (! $this->indexExists('test_attempts', 'test_attempts_user_session_tipe_index')) {
                DB::statement('ALTER TABLE `test_attempts` ADD INDEX `test_attempts_user_session_tipe_index` (`user_id`, `exam_session_id`, `tipe`)');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('exam_participations') && Schema::getConnection()->getDriverName() === 'mysql') {
            $this->dropIndexIfExists('exam_participations', 'exam_participations_user_session_unique');
            if (! $this->indexExists('exam_participations', 'exam_participations_user_id_unique')) {
                DB::statement('ALTER TABLE `exam_participations` ADD UNIQUE `exam_participations_user_id_unique` (`user_id`)');
            }
            $this->dropIndexIfExists('exam_participations', 'exam_participations_user_id_index');

            $this->dropIndexIfExists('knowledge_answers', 'knowledge_answers_question_user_session_stage_unique');
            if (! $this->indexExists('knowledge_answers', 'knowledge_answers_question_user_stage_unique')) {
                DB::statement('ALTER TABLE `knowledge_answers` ADD UNIQUE `knowledge_answers_question_user_stage_unique` (`knowledge_question_id`, `user_id`, `stage`)');
            }
            $this->dropIndexIfExists('knowledge_answers', 'knowledge_answers_user_id_index');

            $this->dropIndexIfExists('attitude_answers', 'attitude_answers_question_user_session_stage_unique');
            if (! $this->indexExists('attitude_answers', 'attitude_answers_question_user_stage_unique')) {
                DB::statement('ALTER TABLE `attitude_answers` ADD UNIQUE `attitude_answers_question_user_stage_unique` (`attitude_question_id`, `user_id`, `stage`)');
            }
            $this->dropIndexIfExists('attitude_answers', 'attitude_answers_user_id_index');
        }

        Schema::table('exam_participations', function (Blueprint $table) {
            if (Schema::hasColumn('exam_participations', 'exam_session_id')) {
                $table->dropConstrainedForeignId('exam_session_id');
            }
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('test_attempts', 'exam_session_id')) {
                $table->dropConstrainedForeignId('exam_session_id');
            }
        });

        Schema::table('attitude_answers', function (Blueprint $table) {
            if (Schema::hasColumn('attitude_answers', 'exam_session_id')) {
                $table->dropConstrainedForeignId('exam_session_id');
            }
        });

        Schema::table('knowledge_answers', function (Blueprint $table) {
            if (Schema::hasColumn('knowledge_answers', 'exam_session_id')) {
                $table->dropConstrainedForeignId('exam_session_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'school_class_id')) {
                $table->dropConstrainedForeignId('school_class_id');
            }
        });

        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('school_classes');
    }

    protected function dropIndexIfExists(string $table, string $index): void
    {
        if ($this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }

    protected function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};
