<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use App\Models\ExamParticipation; // ⬅️ penting

#[Layout('layouts.guest')]
class Login extends Component
{
    public array $form = ['username' => '', 'password' => '', 'remember' => false];

    public function login(): void
    {
        $this->validate([
            'form.username' => ['required', 'string'],
            'form.password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'username' => $this->form['username'],
            'password' => $this->form['password'],
        ], $this->form['remember'])) {
            throw ValidationException::withMessages([
                'form.username' => 'Kredensial tidak cocok.',
            ]);
        }

        request()->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ① Belum tervalidasi → tahan di halaman pending
        if (method_exists($user, 'isValidated') && ! $user->isValidated()) {
            $this->redirect(route('validation.pending'), navigate: true);
            return;
        }

        // ② Sudah valid → redirect per role
        if ($user->isAdmin()) {
            $filamentAdminUrl       = Filament::getPanel('admin')->getUrl();
            $filamentAdminDashboard = route('filament.admin.pages.dashboard');

            // Hormati intended kalau menuju panel admin
            if ($intended = session()->pull('url.intended')) {
                if (Str::startsWith($intended, $filamentAdminUrl)) {
                    $this->redirect($intended, navigate: true);
                    return;
                }
            }

            $this->redirect($filamentAdminDashboard, navigate: true);
            return;
        }

        if ($user->isGuru()) {
            // Hormati intended kalau ke /guru
            if ($intended = session()->pull('url.intended')) {
                if (Str::startsWith($intended, url('/guru'))) {
                    $this->redirect($intended, navigate: true);
                    return;
                }
            }

            $this->redirect(route('guru.dashboard'), navigate: true);
            return;
        }

        if ($user->isSiswa()) {
            $p = \App\Models\ExamParticipation::firstOrCreate(
                ['user_id' => $user->id],
                ['current_step' => 'pretest']
            );
            $step = $p->current_step === 'done' ? 'pretest' : $p->current_step;
            $this->redirect(route("exam.$step"), navigate: true);
            return;
        }

        // Fallback (kalau ada role lain)
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
