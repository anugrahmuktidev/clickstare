<?php

namespace App\Filament\Exports;

use App\Models\Sekolah;
use App\Models\AttitudeQuestion;
use App\Models\KnowledgeQuestion;
use App\Models\User;
use App\Support\KnowledgeTestSummary;
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
        $columns = [
            ExportColumn::make('name')
                ->label('Nama')
                ->state(fn(User $record) => $record->name),
            ExportColumn::make('sekolah')
                ->label('Sekolah')
                ->state(fn(User $record) => $record->sekolah->nama ?? ''),
            ExportColumn::make('nomor_hp')
                ->label('Nomor HP')
                ->state(fn(User $record) => (string) ($record->nisn ?? '')),
            ExportColumn::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->state(fn(User $record) => (string) ($record->jenis_kelamin ?? '')),
            ExportColumn::make('umur')
                ->label('Umur')
                ->state(fn(User $record) => (string) ($record->umur ?? '')),
            ExportColumn::make('kelas')
                ->label('Kelas')
                ->state(fn(User $record) => (string) ($record->kelas ?? '')),
            ExportColumn::make('pekerjaan_orangtua')
                ->label('Pekerjaan Orang Tua')
                ->state(fn(User $record) => (string) ($record->pekerjaan_orangtua ?? '')),
            ExportColumn::make('alamat')
                ->label('Alamat')
                ->state(fn(User $record) => (string) ($record->alamat ?? '')),
            ExportColumn::make('pocket_money_range')
                ->label('Uang Saku')
                ->state(fn(User $record) => static::resolvePocketMoneyRange($record)),
            ExportColumn::make('uses_electric_smoke')
                ->label('Merokok Elektrik')
                ->state(fn(User $record) => static::resolveParticipationYesNo($record, 'uses_electric_smoke')),
            ExportColumn::make('uses_conventional_smoke')
                ->label('Merokok Tembakau/Konvensional')
                ->state(fn(User $record) => static::resolveParticipationYesNo($record, 'uses_conventional_smoke')),
            ExportColumn::make('uses_both_smoke_types')
                ->label('Merokok Elektrik dan Tembakau/Konvensional')
                ->state(fn(User $record) => static::resolveParticipationYesNo($record, 'uses_both_smoke_types')),
        ];

        foreach (['pre', 'post'] as $stage) {
            $label = ucfirst($stage);

            $columns[] = ExportColumn::make("{$stage}_score")
                ->label("{$label} Nilai Pengetahuan")
                ->state(fn(User $record) => static::resolveScore($record, $stage));

            $columns[] = ExportColumn::make("{$stage}_total_benar")
                ->label("{$label} Benar Pengetahuan")
                ->state(fn(User $record) => static::resolveTotalBenar($record, $stage));

            $columns[] = ExportColumn::make("{$stage}_total_soal")
                ->label("{$label} Total Soal Pengetahuan")
                ->state(fn(User $record) => static::resolveTotalSoal($record, $stage));
        }

        $knowledgeQuestions = KnowledgeQuestion::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($knowledgeQuestions as $index => $question) {
            $columns[] = ExportColumn::make('pengetahuan_pre_' . ($index + 1))
                ->label('Pengetahuan Pre ' . ($index + 1))
                ->state(fn(User $record) => static::resolveKnowledgeValue($record, $question->id, 'pre'));
        }

        foreach ($knowledgeQuestions as $index => $question) {
            $columns[] = ExportColumn::make('pengetahuan_post_' . ($index + 1))
                ->label('Pengetahuan Post ' . ($index + 1))
                ->state(fn(User $record) => static::resolveKnowledgeValue($record, $question->id, 'post'));
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
        return User::query()
            ->where('role', 'siswa')
            ->where(function (Builder $query) {
                $query->whereHas('knowledgeAnswers')
                    ->orWhereHas('attitudeAnswers');
            })
            ->with([
                'sekolah:id,nama',
                'examParticipation:user_id,pocket_money_range,uses_electric_smoke,uses_conventional_smoke,uses_both_smoke_types',
                'attitudeAnswers:id,attitude_question_id,user_id,stage,value',
                'knowledgeAnswers:id,knowledge_question_id,user_id,stage,value',
                'knowledgeAnswers.question:id,sort_order,correct_answer',
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

    protected static function resolveKnowledgeValue(User $record, int $questionId, string $stage): string
    {
        $answers = $record->relationLoaded('knowledgeAnswers')
            ? $record->knowledgeAnswers
            : $record->knowledgeAnswers()->get();

        $answer = $answers->first(function ($ans) use ($questionId, $stage) {
            return (int) $ans->knowledge_question_id === $questionId
                && $ans->stage === $stage;
        });

        if (! $answer) {
            return '';
        }

        return match ((string) $answer->value) {
            'SS', 'S', 'BENAR' => 'Benar',
            'STS', 'TS', 'SALAH' => 'Salah',
            default => (string) $answer->value,
        };
    }

    protected static function resolveScore(User $record, string $stage): string
    {
        $summary = KnowledgeTestSummary::summarizeForUser($record, $stage);
        if (! $summary) {
            return '';
        }

        return (string) (int) $summary['score'];
    }

    protected static function resolveTotalBenar(User $record, string $stage): string
    {
        $summary = KnowledgeTestSummary::summarizeForUser($record, $stage);
        if (! $summary) {
            return '';
        }

        return (string) (int) $summary['total_benar'];
    }

    protected static function resolveTotalSoal(User $record, string $stage): string
    {
        $summary = KnowledgeTestSummary::summarizeForUser($record, $stage);
        if (! $summary) {
            return '';
        }

        return (string) (int) $summary['total_soal'];
    }

    protected static function resolvePocketMoneyRange(User $record): string
    {
        $value = $record->examParticipation?->pocket_money_range;

        return match ($value) {
            '5000-10000' => '5.000 - 10.000',
            '10000-20000' => '10.000 - 20.000',
            '20000-50000' => '20.000 - 50.000',
            '>50000' => '> 50.000',
            default => '',
        };
    }

    protected static function resolveParticipationYesNo(User $record, string $field): string
    {
        $value = $record->examParticipation?->{$field};

        return match ($value) {
            true => 'Ya',
            false => 'Tidak',
            default => '',
        };
    }
}
