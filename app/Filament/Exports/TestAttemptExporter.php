<?php

namespace App\Filament\Exports;

use App\Models\Sekolah;
use App\Models\AttitudeQuestion;
use App\Models\ExamSession;
use App\Models\KnowledgeQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use App\Support\KnowledgeTestSummary;
use Filament\Forms\Components\Select;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class TestAttemptExporter extends Exporter
{
    protected static ?string $model = User::class;
    protected static array $currentOptions = [];

    public function __invoke(\Illuminate\Database\Eloquent\Model $record): array
    {
        static::$currentOptions = $this->options;

        return parent::__invoke($record);
    }

    public function getJobQueue(): ?string
    {
        return null;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor(Color::rgb(21, 128, 61))
            ->setShouldWrapText()
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder(static::makeBorder(Color::rgb(20, 83, 45), Border::WIDTH_MEDIUM));
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontColor(Color::rgb(17, 24, 39))
            ->setShouldWrapText()
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder(static::makeBorder(Color::rgb(203, 213, 225), Border::WIDTH_THIN));
    }

    protected static function makeBorder(string $color, string $width): Border
    {
        return new Border(
            new BorderPart(Border::LEFT, $color, $width),
            new BorderPart(Border::RIGHT, $color, $width),
            new BorderPart(Border::TOP, $color, $width),
            new BorderPart(Border::BOTTOM, $color, $width),
        );
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
            ExportColumn::make('sesi')
                ->label('Sesi Test')
                ->state(fn(User $record) => static::resolveExamSessionName($record)),
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
                ->live()
                ->placeholder('Semua')
                ->afterStateUpdated(function (callable $set): void {
                    $set('school_class_id', null);
                    $set('exam_session_id', null);
                }),
            Select::make('school_class_id')
                ->label('Kelas')
                ->options(fn(callable $get) => filled($get('sekolah_id'))
                    ? SchoolClass::query()
                        ->where('sekolah_id', $get('sekolah_id'))
                        ->orderBy('nama')
                        ->pluck('nama', 'id')
                        ->all()
                    : [])
                ->searchable()
                ->preload()
                ->live()
                ->placeholder('Semua kelas')
                ->afterStateUpdated(fn(callable $set) => $set('exam_session_id', null)),
            Select::make('exam_session_id')
                ->label('Sesi Test')
                ->options(fn(callable $get) => filled($get('sekolah_id'))
                    ? ExamSession::query()
                        ->where('sekolah_id', $get('sekolah_id'))
                        ->orderBy('nama')
                        ->pluck('nama', 'id')
                        ->all()
                    : [])
                ->searchable()
                ->preload()
                ->placeholder('Semua sesi'),
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
                'schoolClass:id,nama',
                'attempts:id,user_id,exam_session_id,tipe,total_soal,total_benar,score,updated_at',
                'examParticipations:id,user_id,exam_session_id,pocket_money_range,uses_electric_smoke,uses_conventional_smoke,uses_both_smoke_types,updated_at',
                'examParticipations.examSession:id,nama',
                'attitudeAnswers:id,attitude_question_id,user_id,exam_session_id,stage,value',
                'knowledgeAnswers:id,knowledge_question_id,user_id,exam_session_id,stage,value',
                'knowledgeAnswers.question:id,sort_order,correct_answer',
            ]);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'File export hasil tes sudah siap diunduh.';
    }

    protected static function resolveAttitudeValue(User $record, int $questionId, string $stage): string
    {
        $examSessionId = static::currentExamSessionId($record);
        $answers = $record->relationLoaded('attitudeAnswers')
            ? $record->attitudeAnswers
            : $record->attitudeAnswers()->get();

        $answer = $answers->first(function ($ans) use ($questionId, $stage, $examSessionId) {
            return (int) $ans->attitude_question_id === $questionId
                && $ans->stage === $stage
                && (! $examSessionId || (int) $ans->exam_session_id === $examSessionId);
        });

        return $answer ? (string) $answer->value : '';
    }

    protected static function resolveKnowledgeValue(User $record, int $questionId, string $stage): string
    {
        $examSessionId = static::currentExamSessionId($record);
        $answers = $record->relationLoaded('knowledgeAnswers')
            ? $record->knowledgeAnswers
            : $record->knowledgeAnswers()->get();

        $answer = $answers->first(function ($ans) use ($questionId, $stage, $examSessionId) {
            return (int) $ans->knowledge_question_id === $questionId
                && $ans->stage === $stage
                && (! $examSessionId || (int) $ans->exam_session_id === $examSessionId);
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
        $attempt = static::currentAttempt($record, $stage);
        if ($attempt) {
            return (string) (int) $attempt->score;
        }

        $summary = KnowledgeTestSummary::summarizeForUser($record, $stage, static::currentExamSessionId($record));
        if (! $summary) {
            return '';
        }

        return (string) (int) $summary['score'];
    }

    protected static function resolveTotalBenar(User $record, string $stage): string
    {
        $attempt = static::currentAttempt($record, $stage);
        if ($attempt) {
            return (string) (int) $attempt->total_benar;
        }

        $summary = KnowledgeTestSummary::summarizeForUser($record, $stage, static::currentExamSessionId($record));
        if (! $summary) {
            return '';
        }

        return (string) (int) $summary['total_benar'];
    }

    protected static function resolveTotalSoal(User $record, string $stage): string
    {
        $attempt = static::currentAttempt($record, $stage);
        if ($attempt) {
            return (string) (int) $attempt->total_soal;
        }

        $summary = KnowledgeTestSummary::summarizeForUser($record, $stage, static::currentExamSessionId($record));
        if (! $summary) {
            return '';
        }

        return (string) (int) $summary['total_soal'];
    }

    protected static function resolvePocketMoneyRange(User $record): string
    {
        $value = static::currentParticipation($record)?->pocket_money_range;

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
        $value = static::currentParticipation($record)?->{$field};

        return match ($value) {
            true => 'Ya',
            false => 'Tidak',
            default => '',
        };
    }

    protected static function resolveExamSessionName(User $record): string
    {
        return static::currentParticipation($record)?->examSession?->nama ?? '';
    }

    protected static function currentExamSessionId(User $record): ?int
    {
        return static::currentParticipation($record)?->exam_session_id
            ? (int) static::currentParticipation($record)->exam_session_id
            : null;
    }

    protected static function currentParticipation(User $record)
    {
        if (! $record->relationLoaded('examParticipations')) {
            return $record->examParticipation;
        }

        $optionSessionId = static::$currentOptions['exam_session_id'] ?? null;

        if ($optionSessionId) {
            return $record->examParticipations->firstWhere('exam_session_id', (int) $optionSessionId);
        }

        return $record->examParticipations
            ->sortByDesc(fn($participation) => $participation->updated_at?->timestamp ?? 0)
            ->first();
    }

    protected static function currentAttempt(User $record, string $stage)
    {
        $attempts = $record->relationLoaded('attempts')
            ? $record->attempts
            : $record->attempts()->get();

        $examSessionId = static::currentExamSessionId($record);

        return $attempts
            ->where('tipe', $stage)
            ->when($examSessionId, fn ($attempts) => $attempts->where('exam_session_id', $examSessionId))
            ->sortByDesc(fn ($attempt) => $attempt->updated_at?->timestamp ?? 0)
            ->first();
    }
}
