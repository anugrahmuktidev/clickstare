<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ExamParticipation;
// app/Http/Middleware/EnsureExamStep.php
class EnsureExamStep
{
    public function handle(Request $request, Closure $next, string $requiredStep)
    {
        $user = $request->user();

        $p = ExamParticipation::firstOrCreate(
            ['user_id' => $user->id],
            ['current_step' => 'pretest']
        );

        $order = ['pretest' => 1, 'video' => 2, 'posttest' => 3, 'done' => 4];

        // Jika sudah selesai → arahkan ke halaman akhir
        if ($p->current_step === 'done') {
            return redirect()->route('education.index');
        }

        // Cegah loncat maju
        if ($order[$requiredStep] > $order[$p->current_step]) {
            return redirect()->route("exam.{$p->current_step}")
                ->with('error', 'Selesaikan langkah sebelumnya terlebih dahulu.');
        }

        // Cegah mundur
        if ($order[$requiredStep] < $order[$p->current_step]) {
            return redirect()->route("exam.{$p->current_step}")
                ->with('info', 'Anda tidak bisa kembali ke langkah sebelumnya.');
        }

        return $next($request);
    }
}
