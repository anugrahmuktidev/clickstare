<?php

namespace App\Filament\Resources\TestAttempts\Pages;

use App\Filament\Resources\TestAttempts\TestAttemptResource;
use App\Models\AttitudeAnswer;
use App\Models\KnowledgeAnswer;
use App\Models\KnowledgeQuestion;
use App\Support\KnowledgeTestSummary;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTestAttempt extends EditRecord
{
    protected static string $resource = TestAttemptResource::class;
    protected array $pendingKnowledgeAnswers = [];
    protected array $pendingAttitudeAnswers = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['knowledge_answers'] = KnowledgeAnswer::query()
            ->where('user_id', $this->record->user_id)
            ->where('stage', $this->record->tipe)
            ->when(
                $this->record->exam_session_id,
                fn ($query) => $query->where('exam_session_id', $this->record->exam_session_id),
                fn ($query) => $query->whereNull('exam_session_id')
            )
            ->get()
            ->mapWithKeys(fn (KnowledgeAnswer $answer): array => [
                $answer->knowledge_question_id => KnowledgeTestSummary::normalizeAnswerValue($answer->value),
            ])
            ->all();

        $data['attitude_answers'] = AttitudeAnswer::query()
            ->where('user_id', $this->record->user_id)
            ->where('stage', $this->record->tipe)
            ->when(
                $this->record->exam_session_id,
                fn ($query) => $query->where('exam_session_id', $this->record->exam_session_id),
                fn ($query) => $query->whereNull('exam_session_id')
            )
            ->pluck('value', 'attitude_question_id')
            ->all();

        if (blank($data['total_soal'] ?? null)) {
            $data['total_soal'] = KnowledgeQuestion::query()->where('is_active', true)->count();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingKnowledgeAnswers = (array) ($data['knowledge_answers'] ?? []);
        $this->pendingAttitudeAnswers = (array) ($data['attitude_answers'] ?? []);
        unset($data['knowledge_answers']);
        unset($data['attitude_answers']);

        $questions = KnowledgeQuestion::query()
            ->where('is_active', true)
            ->get(['id', 'correct_answer']);

        $totalSoal = $questions->count();
        $totalBenar = $questions->filter(function (KnowledgeQuestion $question): bool {
            $selected = KnowledgeTestSummary::normalizeAnswerValue($this->pendingKnowledgeAnswers[$question->id] ?? null);
            $correct = KnowledgeTestSummary::normalizeAnswerValue($question->correct_answer);

            return $selected !== null && $correct !== null && $selected === $correct;
        })->count();

        $data['total_soal'] = $totalSoal;
        $data['total_benar'] = $totalBenar;
        $data['score'] = $totalSoal > 0 ? (int) round(($totalBenar / $totalSoal) * 100) : 0;

        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->pendingKnowledgeAnswers as $questionId => $value) {
            $normalizedValue = KnowledgeTestSummary::normalizeAnswerValue($value);

            if (! $normalizedValue) {
                continue;
            }

            KnowledgeAnswer::query()->updateOrCreate(
                [
                    'knowledge_question_id' => (int) $questionId,
                    'user_id' => $this->record->user_id,
                    'exam_session_id' => $this->record->exam_session_id,
                    'stage' => $this->record->tipe,
                ],
                [
                    'value' => $normalizedValue,
                ]
            );
        }

        foreach ($this->pendingAttitudeAnswers as $questionId => $value) {
            if (! in_array($value, ['STS', 'TS', 'S', 'SS'], true)) {
                continue;
            }

            AttitudeAnswer::query()->updateOrCreate(
                [
                    'attitude_question_id' => (int) $questionId,
                    'user_id' => $this->record->user_id,
                    'exam_session_id' => $this->record->exam_session_id,
                    'stage' => $this->record->tipe,
                ],
                [
                    'value' => $value,
                ]
            );
        }
    }
}
