<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ExamParticipation;
use App\Models\ExamSession;
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

        $sessionId = (int) $request->session()->get('active_exam_session_id');
        $examSession = ExamSession::query()
            ->whereKey($sessionId)
            ->where('sekolah_id', $user->sekolah_id)
            ->where('is_active', true)
            ->first();

        if (! $examSession) {
            return redirect()->route('education.index')
                ->with('error', 'Pilih sesi test aktif terlebih dahulu.');
        }

        $p = ExamParticipation::firstOrCreate(
            [
                'user_id' => $user->id,
                'exam_session_id' => $examSession->id,
            ],
            ['current_step' => 'pretest']
        );

        if (in_array($requiredStep, $posttestSteps, true) && ! $p->video_watched_at) {
            return redirect()->route('education.index')
                ->with('error', 'Tonton video edukasi sampai selesai sebelum melanjutkan ke posttest.');
        }

        $order = [
            'pretest'               => 1,
            'pengetahuan_test'      => 2,
            'sikap'                 => 3,
            'video'                 => 4,
            'posttest'              => 5,
            'pengetahuan_test_post' => 6,
            'sikap_post'            => 7,
            'done'                  => 8,
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
