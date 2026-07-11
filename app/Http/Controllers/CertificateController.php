<?php

namespace App\Http\Controllers;

use App\Support\KnowledgeTestSummary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $attempt = KnowledgeTestSummary::summarizeForUser($user, 'post');

        if (! $attempt) {
            abort(403, 'Sertifikat hanya tersedia setelah Anda menyelesaikan posttest.');
        }

        $backgroundPath = public_path('images/sertifikat.png');
        abort_unless(File::exists($backgroundPath), 500, 'Template sertifikat tidak ditemukan.');

        $backgroundDataUri = 'data:image/png;base64,' . base64_encode(File::get($backgroundPath));

        $pdf = Pdf::loadView('education.certificate', [
            'user' => $user,
            'attempt' => (object) $attempt,
            'issuedAt' => now(),
            'backgroundDataUri' => $backgroundDataUri,
        ])->setPaper('a4', 'landscape');

        $filename = 'sertifikat-' . Str::slug($user->name) . '.pdf';

        return $pdf->download($filename);
    }
}
