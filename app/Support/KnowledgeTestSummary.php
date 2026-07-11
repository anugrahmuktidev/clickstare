<?php

namespace App\Support;

use App\Models\KnowledgeAnswer;
use App\Models\User;
use Illuminate\Support\Carbon;

class KnowledgeTestSummary
{
    public static function summarizeForUser(User $user, string $stage): ?array
    {
        $answers = $user->relationLoaded('knowledgeAnswers')
            ? $user->knowledgeAnswers->where('stage', $stage)->values()
            : KnowledgeAnswer::query()
                ->where('user_id', $user->id)
                ->where('stage', $stage)
                ->with('question:id,correct_answer')
                ->get();

        if ($answers->isEmpty()) {
            return null;
        }

        $answers->loadMissing('question:id,correct_answer');

        $scoredAnswers = $answers->filter(fn (KnowledgeAnswer $answer) => $answer->question !== null)->values();
        $totalSoal = $scoredAnswers->count();

        if ($totalSoal === 0) {
            return null;
        }

        $totalBenar = $scoredAnswers->filter(function (KnowledgeAnswer $answer) {
            $selected = self::normalizeAnswerValue($answer->value);
            $correct = self::normalizeAnswerValue($answer->question?->correct_answer);

            return $selected !== null && $correct !== null && $selected === $correct;
        })->count();

        $answeredAt = $answers->max(function (KnowledgeAnswer $answer) {
            return optional($answer->updated_at)->timestamp ?? optional($answer->created_at)->timestamp ?? 0;
        });

        return [
            'total_soal' => $totalSoal,
            'total_benar' => $totalBenar,
            'score' => (int) round(($totalBenar / max(1, $totalSoal)) * 100),
            'answered_at' => $answeredAt ? Carbon::createFromTimestamp($answeredAt) : null,
        ];
    }

    public static function answerBySortOrder(User $user, string $stage, int $sortOrder): string
    {
        $answers = $user->relationLoaded('knowledgeAnswers')
            ? $user->knowledgeAnswers->where('stage', $stage)->values()
            : KnowledgeAnswer::query()
                ->where('user_id', $user->id)
                ->where('stage', $stage)
                ->with('question:id,sort_order')
                ->get();

        $answers->loadMissing('question:id,sort_order');

        $answer = $answers->first(function (KnowledgeAnswer $answer) use ($sortOrder) {
            return (int) ($answer->question?->sort_order ?? 0) === $sortOrder;
        });

        return self::label($answer?->value);
    }

    public static function label(?string $value): string
    {
        return match (self::normalizeAnswerValue($value)) {
            'BENAR' => 'Benar',
            'SALAH' => 'Salah',
            default => '',
        };
    }

    public static function normalizeAnswerValue(?string $value): ?string
    {
        return match ($value) {
            'BENAR', 'SALAH' => $value,
            'S', 'SS' => 'BENAR',
            'TS', 'STS' => 'SALAH',
            default => null,
        };
    }
}
