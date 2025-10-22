<?php

namespace App\Livewire\Auth;

use App\Models\Sekolah;
use App\Models\User;
use App\Models\ExamParticipation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Register extends Component
{
    // role default siswa
    public string $role = 'siswa';

    // field umum
    public ?string $name = null;
    public ?int $sekolah_id = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    // field gabungan (NISN/NIP)
    public ?string $id_number = null;

    // field khusus (opsional)
    public ?int $umur = null;
    public ?string $kelas = null;     // siswa
    public ?string $jabatan = null;   // guru
    public ?string $jenis_kelamin = null; // siswa
    public ?string $pekerjaan_orangtua = null; // siswa
    public ?string $alamat = null; // siswa

    public function register(): void
    {
        // validasi dinamis berdasar role
        $rules = [
            'role'       => ['required', 'in:siswa,guru'],
            'name'       => ['required', 'string', 'max:255'],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'password'   => ['required', 'min:8', 'confirmed'],
        ];

        if ($this->role === 'siswa') {
            $rules += [
                'id_number' => ['required', 'regex:/^\d{10,15}$/', 'unique:users,nisn', 'unique:users,username'],
                'umur'      => ['nullable', 'integer', 'min:7', 'max:25'],
                'kelas'     => ['nullable', 'string', 'max:20'],
                'jenis_kelamin'      => ['required', 'in:laki-laki,perempuan'],
                'pekerjaan_orangtua' => ['nullable', 'string', 'max:120'],
                'alamat'             => ['nullable', 'string', 'max:500'],
            ];
        } else { // guru
            $rules += [
                'id_number' => ['required', 'digits_between:8,20', 'unique:users,nip', 'unique:users,username'],
                'umur'      => ['nullable', 'integer', 'min:18', 'max:70'],
                'jabatan'   => ['required', 'in:walikelas,guru,kepsek,bk'],
            ];
        }

        $data = $this->validate($rules);

        // siapkan atribut user
        $attrs = [
            'username'     => $data['id_number'],
            'role'         => $data['role'],
            'name'         => $data['name'],
            'sekolah_id'   => $data['sekolah_id'],
            'password'     => Hash::make($data['password']),
            'is_validated' => false,          // default menunggu validasi admin
            'validated_at' => null,
        ];

        if ($this->role === 'siswa') {
            $attrs['nisn']  = $data['id_number'];
            $attrs['umur']  = $data['umur'] ?? null;
            $attrs['kelas'] = $data['kelas'] ?? null;
            $attrs['jenis_kelamin']      = $data['jenis_kelamin'];
            $attrs['pekerjaan_orangtua'] = $data['pekerjaan_orangtua'] ?? null;
            $attrs['alamat']             = $data['alamat'] ?? null;
        } else {
            $attrs['nip']     = $data['id_number'];
            $attrs['umur']    = $data['umur'] ?? null;
            $attrs['jabatan'] = $data['jabatan'] ?? null;
        }

        $user = User::create($attrs);

        // login & regen sesi
        Auth::login($user);
        request()->session()->regenerate();

        // jika belum tervalidasi → tahan di halaman pending
        if (method_exists($user, 'isValidated') && ! $user->isValidated()) {
            $this->redirect(route('validation.pending'), navigate: true);
            return;
        }

        // ===== Redirect per role setelah tervalidasi =====
        if ($user->role === 'guru') {
            $this->redirect(route('guru.dashboard'), navigate: true);
            return;
        }

        if ($user->role === 'siswa') {

            // siapkan progres exam (agar middleware step bekerja)
            ExamParticipation::firstOrCreate(
                ['user_id' => $user->id],
                ['current_step' => 'pretest']
            );

            // arahkan ke pretest
            $this->redirect(route('exam.pretest'), navigate: true);
            return;
        }

        // fallback (kalau ada role lain)
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register', [
            'sekolahs' => Sekolah::orderBy('nama')->get(),
        ]);
    }
}
