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

        $pretestSteps = ['pretest', 'sikap', 'pengetahuan_test'];
        $posttestSteps = ['posttest', 'sikap_post', 'pengetahuan_test_post'];

        if (in_array($requiredStep, $pretestSteps, true) && ! (bool) ($user?->sekolah?->is_pretest_enabled ?? false)) {
            abort(403, 'Pretest untuk sekolah Anda sedang ditutup oleh admin.');
        }

        if (in_array($requiredStep, $posttestSteps, true) && ! (bool) ($user?->sekolah?->is_posttest_enabled ?? false)) {
            abort(403, 'Posttest untuk sekolah Anda sedang ditutup oleh admin.');
        }

        $p = ExamParticipation::firstOrCreate(
            ['user_id' => $user->id],
            ['current_step' => 'pretest']
        );

        $order = [
            'pretest'   => 1,
            'sikap'     => 2,
            'pengetahuan_test' => 3,
            'video'     => 4,
            'posttest'  => 5,
            'sikap_post'=> 6,
            'pengetahuan_test_post' => 7,
            'done'      => 8,
        ];

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
