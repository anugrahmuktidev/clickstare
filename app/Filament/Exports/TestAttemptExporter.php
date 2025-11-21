<?php

namespace App\Filament\Exports;

use App\Models\Question;
use App\Models\Sekolah;
use App\Models\AttitudeQuestion;
use App\Models\TestAttempt;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TestAttemptExporter extends Exporter
{
    protected static ?string $model = User::class;

    public function getJobQueue(): ?string
    {
        return null;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getColumns(): array
    {
        $maxNomor = (int) (Question::max('nomor') ?? 0);
        if ($maxNomor < 1) {
            $maxNomor = 10;
        }

        $perSoal = function (string $stage, int $nomor) {
            return function (User $record) use ($stage, $nomor) {
                $attempt = static::resolveAttempt($record, $stage);
                if (! $attempt) {
                    return '';
                }

                $answers = $attempt->relationLoaded('answers')
                    ? $attempt->answers
                    : $attempt->answers()->with('question:id,nomor')->get();

                foreach ($answers as $answer) {
                    $n = (int) ($answer->question->nomor ?? 0);
                    if ($n === $nomor) {
                        return $answer->is_correct ? 'B' : 'S';
                    }
                }

                return '';
            };
        };

        $columns = [
            ExportColumn::make('name')
                ->label('Nama')
                ->state(fn(User $record) => $record->name),
            ExportColumn::make('sekolah')
                ->label('Sekolah')
                ->state(fn(User $record) => $record->sekolah->nama ?? ''),
        ];

        foreach (['pre', 'post'] as $stage) {
            $label = ucfirst($stage);

            $columns[] = ExportColumn::make("{$stage}_score")
                ->label("{$label} Nilai")
                ->state(fn(User $record) => static::resolveScore($record, $stage));

            $columns[] = ExportColumn::make("{$stage}_total_benar")
                ->label("{$label} Total Benar")
                ->state(fn(User $record) => static::resolveTotalBenar($record, $stage));

            $columns[] = ExportColumn::make("{$stage}_total_soal")
                ->label("{$label} Total Soal")
                ->state(fn(User $record) => static::resolveTotalSoal($record, $stage));
        }

        foreach (['pre', 'post'] as $stage) {
            for ($i = 1; $i <= $maxNomor; $i++) {
                $columns[] = ExportColumn::make("{$stage}_p{$i}")
                    ->label(strtoupper($stage) . ' P' . $i)
                    ->state($perSoal($stage, $i));
            }
        }

        $attitudeQuestions = AttitudeQuestion::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($attitudeQuestions as $index => $question) {
            $columns[] = ExportColumn::make('sikap_pre_' . ($index + 1))
                ->label('Sikap Pre ' . ($index + 1))
                ->state(fn(User $record) => static::resolveAttitudeValue($record, $question->id, 'pre'));
        }

        foreach ($attitudeQuestions as $index => $question) {
            $columns[] = ExportColumn::make('sikap_post_' . ($index + 1))
                ->label('Sikap Post ' . ($index + 1))
                ->state(fn(User $record) => static::resolveAttitudeValue($record, $question->id, 'post'));
        }

        return $columns;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('sekolah_id')
                ->label('Sekolah')
                ->options(fn() => Sekolah::orderBy('nama')->pluck('nama', 'id')->all())
                ->searchable()
                ->preload()
                ->placeholder('Semua'),
        ];
    }

    protected function resolveColumnsForOptions(): array
    {
        $columns = static::getColumns();
        $testType = Str::lower($this->options['test_type'] ?? 'all');

        if ($testType === 'all') {
            return $columns;
        }

        return array_values(array_filter($columns, function (ExportColumn $column) use ($testType) {
            $name = Str::lower($column->getName());

            if (! Str::contains($name, 'pre') && ! Str::contains($name, 'post')) {
                return true;
            }

            return Str::contains($name, $testType);
        }));
    }

    public function getCachedColumns(): array
    {
        return $this->cachedColumns ??= array_reduce($this->resolveColumnsForOptions(), function (array $carry, ExportColumn $column): array {
            $carry[$column->getName()] = $column->exporter($this);

            return $carry;
        }, []);
    }

    public static function modifyQuery(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();

        $userSubQuery = (clone $query)
            ->select("{$table}.user_id")
            ->groupBy("{$table}.user_id");

        return User::query()
            ->whereIn('id', $userSubQuery)
            ->with([
                'sekolah:id,nama',
                'attempts.answers' => fn($q) => $q->with('question:id,nomor'),
                'attitudeAnswers:id,attitude_question_id,user_id,stage,value',
            ]);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'File export hasil tes sudah siap diunduh.';
    }

    protected static function resolveAttitudeValue(User $record, int $questionId, string $stage): string
    {
        $answers = $record->relationLoaded('attitudeAnswers')
            ? $record->attitudeAnswers
            : $record->attitudeAnswers()->get();

        $answer = $answers->first(function ($ans) use ($questionId, $stage) {
            return (int) $ans->attitude_question_id === $questionId
                && $ans->stage === $stage;
        });

        return $answer ? (string) $answer->value : '';
    }

    protected static function resolveAttempt(User $record, string $stage): ?TestAttempt
    {
        $attempts = $record->relationLoaded('attempts')
            ? $record->attempts
            : $record->attempts()->get();

        return $attempts->firstWhere('tipe', $stage);
    }

    protected static function resolveScore(User $record, string $stage): string
    {
        $attempt = static::resolveAttempt($record, $stage);

        if (! $attempt || is_null($attempt->score)) {
            return '';
        }

        return (string) (int) $attempt->score;
    }

    protected static function resolveTotalBenar(User $record, string $stage): string
    {
        $attempt = static::resolveAttempt($record, $stage);

        if (! $attempt) {
            return '';
        }

        if (! is_null($attempt->total_benar)) {
            return (string) (int) $attempt->total_benar;
        }

        $answers = $attempt->relationLoaded('answers')
            ? $attempt->answers
            : $attempt->answers()->get();

        return (string) $answers->where('is_correct', true)->count();
    }

    protected static function resolveTotalSoal(User $record, string $stage): string
    {
        $attempt = static::resolveAttempt($record, $stage);

        if (! $attempt) {
            return '';
        }

        if (! is_null($attempt->total_soal)) {
            return (string) (int) $attempt->total_soal;
        }

        $answers = $attempt->relationLoaded('answers')
            ? $attempt->answers
            : $attempt->answers()->get();

        return (string) $answers->count();
    }
}
