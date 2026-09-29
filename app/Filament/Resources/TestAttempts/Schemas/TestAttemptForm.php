<?php

namespace App\Filament\Resources\TestAttempts\Schemas;

use App\Models\AttitudeAnswer;
use App\Models\AttitudeQuestion;
use App\Models\KnowledgeAnswer;
use App\Models\KnowledgeQuestion;
use App\Support\KnowledgeTestSummary;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        $knowledgeQuestions = KnowledgeQuestion::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $attitudeQuestions = AttitudeQuestion::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Peserta')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('tipe')
                    ->label('Jenis Tes')
                    ->options([
                        'pre' => 'Pretest',
                        'post' => 'Posttest',
                    ])
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('total_soal')
                    ->label('Total Soal')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('total_benar')
                    ->label('Jumlah Benar')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(fn ($record) => $record?->total_soal ?? 999999)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                TextInput::make('score')
                    ->label('Score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Section::make('Jawaban Pengetahuan')
                    ->description('Ubah jawaban per soal, lalu simpan. Jumlah benar dan score akan dihitung ulang otomatis.')
                    ->schema(
                        $knowledgeQuestions
                            ->map(fn (KnowledgeQuestion $question) => Radio::make('knowledge_answers.' . $question->id)
                                ->label(($question->sort_order ?: $question->id) . '. ' . $question->teks)
                                ->options([
                                    'BENAR' => 'Benar',
                                    'SALAH' => 'Salah',
                                ])
                                ->inline()
                                ->live()
                                ->default(fn ($record) => static::resolveKnowledgeAnswerValue($record, $question))
                                ->afterStateUpdated(fn (callable $get, callable $set) => static::refreshScorePreview($get, $set)))
                            ->all()
                    )
                    ->columns(1)
                    ->columnSpanFull(),
                Section::make('Jawaban Sikap')
                    ->description('Jawaban sikap ikut disimpan dan muncul pada export, tetapi tidak memengaruhi score pengetahuan.')
                    ->schema(
                        $attitudeQuestions
                            ->map(fn (AttitudeQuestion $question) => Radio::make('attitude_answers.' . $question->id)
                                ->label(($question->sort_order ?: $question->id) . '. ' . $question->teks)
                                ->options([
                                    'STS' => 'Sangat Tidak Setuju',
                                    'TS' => 'Tidak Setuju',
                                    'S' => 'Setuju',
                                    'SS' => 'Sangat Setuju',
                                ])
                                ->inline()
                                ->default(fn ($record) => static::resolveAttitudeAnswerValue($record, $question)))
                            ->all()
                    )
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    protected static function resolveKnowledgeAnswerValue($record, KnowledgeQuestion $question): ?string
    {
        if (! $record) {
            return null;
        }

        $value = KnowledgeAnswer::query()
            ->where('user_id', $record->user_id)
            ->where('knowledge_question_id', $question->id)
            ->where('stage', $record->tipe)
            ->when(
                $record->exam_session_id,
                fn ($query) => $query->where('exam_session_id', $record->exam_session_id),
                fn ($query) => $query->whereNull('exam_session_id')
            )
            ->value('value');

        return KnowledgeTestSummary::normalizeAnswerValue($value);
    }

    protected static function refreshScorePreview(callable $get, callable $set): void
    {
        $answers = (array) ($get('knowledge_answers') ?? []);
        $questions = KnowledgeQuestion::query()
            ->where('is_active', true)
            ->get(['id', 'correct_answer']);

        $totalSoal = $questions->count();
        $totalBenar = $questions->filter(function (KnowledgeQuestion $question) use ($answers): bool {
            $selected = KnowledgeTestSummary::normalizeAnswerValue($answers[$question->id] ?? null);
            $correct = KnowledgeTestSummary::normalizeAnswerValue($question->correct_answer);

            return $selected !== null && $correct !== null && $selected === $correct;
        })->count();

        $set('total_soal', $totalSoal);
        $set('total_benar', $totalBenar);
        $set('score', $totalSoal > 0 ? (int) round(($totalBenar / $totalSoal) * 100) : 0);
    }

    protected static function resolveAttitudeAnswerValue($record, AttitudeQuestion $question): ?string
    {
        if (! $record) {
            return null;
        }

        return AttitudeAnswer::query()
            ->where('user_id', $record->user_id)
            ->where('attitude_question_id', $question->id)
            ->where('stage', $record->tipe)
            ->when(
                $record->exam_session_id,
                fn ($query) => $query->where('exam_session_id', $record->exam_session_id),
                fn ($query) => $query->whereNull('exam_session_id')
            )
            ->value('value');
    }
}
