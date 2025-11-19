<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $minCorrect = (int) config('exam.certificate_min_correct', 8);

        $attempt = TestAttempt::query()
            ->where('user_id', $user->id)
            ->where('tipe', 'post')
            ->where('total_benar', '>=', $minCorrect)
            ->latest('created_at')
            ->first();

        if (!$attempt) {
            abort(403, 'Sertifikat hanya tersedia setelah Anda lulus posttest.');
        }

        $pdf = Pdf::loadView('education.certificate', [
            'user' => $user,
            'attempt' => $attempt,
            'issuedAt' => now(),
        ])->setPaper('a4', 'landscape');

        $filename = 'sertifikat-' . Str::slug($user->name) . '.pdf';

        return $pdf->download($filename);
    }
}
