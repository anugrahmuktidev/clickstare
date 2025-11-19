<?php

use App\Http\Controllers\CertificateController;
use App\Livewire\Auth\Login;
use App\Livewire\Exam\Video;
use App\Livewire\Exam\Pretest;
use App\Livewire\Auth\Register;
use App\Livewire\Exam\Posttest;
use App\Livewire\Exam\VideoExam;
use App\Livewire\Education\Watch;
use App\Livewire\Education\Dashboard;
use App\Livewire\Guru\ValidateStudents;
use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Journals\Index as JournalIndex;
use App\Livewire\Posts\Index as PostIndex;
use App\Livewire\Posts\Show as PostShow;
use App\Models\Journal;
use App\Models\Post;
use App\Models\HeroSlide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// use App\Livewire\Admin\Dashboard as AdminDashboard;
// use App\Livewire\Admin\Sekolah\Index as SekolahIndex;

// routes/web.php
Route::get('/', function () {
    $posts = Post::query()
        ->latest('updated_at')
        ->take(4)
        ->get();

    $journals = Journal::query()
        ->latest('updated_at')
        ->take(6)
        ->get();

    $slides = HeroSlide::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return view('landing', [
        'posts' => $posts,
        'journals' => $journals,
        'slides' => $slides,
    ]);
})->name('landing');

Route::get('/posts', PostIndex::class)->name('posts.index');
Route::get('/posts/{post}', PostShow::class)->name('posts.show');

Route::get('/journals', JournalIndex::class)->name('journals.index');
Route::view('/risiko-rokok', 'guest.risk')->name('risk.info');

// =====================
// Halaman Guest
// =====================
Route::middleware(['guest'])->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});


// =====================
// Halaman Admin
// =====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {});


// Validation pending (user sudah login tapi belum divalidasi)
Route::middleware('auth')->group(function () {
    Route::get('/validation-pending', function () {
        return view('livewire.auth.validation-pending');
    })->name('validation.pending');

    // endpoint cek status (untuk$ auto-redirect)
    Route::get('/validation-status', function () {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) return response()->json(['validated' => false]);

        if (! $user->isValidated()) {
            return response()->json(['validated' => false]);
        }

        if ($user->isAdmin()) {
            return response()->json(['validated' => true, 'redirect' => route('filament.admin.pages.dashboard')]);
        }
        if ($user->isGuru()) {
            return response()->json(['validated' => true, 'redirect' => route('guru.dashboard')]);
        }

        // siswa → ke pretest exam tunggal
        return response()->json([
            'validated' => true,
            'redirect'  => route('exam.pretest'),
        ]);
    })->name('validation.status');
});
// SISWA
Route::middleware(['auth', 'role:siswa', 'validated'])->group(function () {
    Route::get('/exam/pretest',  Pretest::class)->name('exam.pretest')->middleware('step:pretest');
    Route::get('/exam/video',    VideoExam::class)->name('exam.video')->middleware('step:video');
    Route::get('/exam/posttest', Posttest::class)->name('exam.posttest')->middleware('step:posttest');
    Route::get('/education/certificate', CertificateController::class)->name('education.certificate.download');
});
Route::middleware(['auth', 'validated'])->group(function () {
    Route::get('/education', Dashboard::class)->name('education.index');
    // ⇩ route yang hilang
    Route::get('/education/watch/{video}', Watch::class)
        ->name('education.watch');
});


// GURU
Route::middleware(['auth', 'role:guru', 'validated'])->group(function () {
    Route::get('/dashboard/guru', GuruDashboard::class)->name('guru.dashboard');
    Route::get('guru/validasi', ValidateStudents::class)->name('guru.validate');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('landing');
})->middleware('auth')->name('logout');
