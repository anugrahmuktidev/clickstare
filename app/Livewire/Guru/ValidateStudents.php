<?php

namespace App\Livewire\Guru;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\ParticipantValidation;

use Livewire\Attributes\Layout;


#[Layout('layouts.guest')]


class ValidateStudents extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $kelas = null;

    /** @var array<int> */
    public array $checked = [];
    public bool $selectAllPage = false;

    // ukuran halaman
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'kelas'  => ['except' => null],
        'page'   => ['except' => 1],
    ];

    public function mount(): void
    {
        $this->checked = [];
        $this->selectAllPage = false;
    }

    /** Query dasar siswa pending di sekolah guru yang login */
    protected function baseQuery()
    {
        $guru = Auth::user();

        return User::query()
            ->where('role', 'siswa')
            ->where('sekolah_id', $guru->sekolah_id)
            ->where(function ($q) {
                // pending versi user flag
                $q->where('is_validated', false)->orWhereNull('is_validated');
            })
            ->when($this->search !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)
                        ->orWhere('username', 'like', $term)
                        ->orWhere('nisn', 'like', $term);
                });
            })
            ->when($this->kelas !== null && $this->kelas !== '', function ($q) {
                $q->where('kelas', $this->kelas);
            })
            ->orderBy('name');
    }

    /** Ambil ID siswa di halaman aktif */
    protected function currentPageIds(): array
    {
        return $this->baseQuery()
            ->clone()
            ->paginate($this->perPage, ['id'])
            ->getCollection()
            ->pluck('id')
            ->all();
    }

    /** Saat checkbox "pilih semua halaman ini" berubah */
    public function updatedSelectAllPage(bool $value): void
    {
        $ids = $this->currentPageIds();

        if ($value) {
            // gabungkan tanpa duplikat
            $this->checked = array_values(array_unique(array_merge($this->checked, $ids)));
        } else {
            // buang id yang ada di halaman aktif
            $this->checked = array_values(array_diff($this->checked, $ids));
        }
    }

    /** Reset selectAllPage tiap pindah halaman / filter */
    public function updatedPage(): void
    {
        $this->selectAllPage = false;
    }
    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectAllPage = false;
    }
    public function updatedKelas(): void
    {
        $this->resetPage();
        $this->selectAllPage = false;
    }

    /** Approve bulk */
    public function approveSelected(): void
    {
        $ids = array_map('intval', $this->checked);
        if (empty($ids)) return;

        $guru = Auth::user();

        // batasi tetap satu sekolah & role siswa
        $targetIds = User::whereIn('id', $ids)
            ->where('role', 'siswa')
            ->where('sekolah_id', $guru->sekolah_id)
            ->pluck('id');

        // update users (bypass fillable bisa juga, tapi kalau sudah fillable, ini aman)
        User::whereIn('id', $targetIds)->update([
            'is_validated' => true,
            'validated_at' => now(),
        ]);

        // update/insert participant_validations
        foreach ($targetIds as $uid) {
            ParticipantValidation::updateOrCreate(
                ['user_id' => $uid],
                ['status' => 'valid', 'validated_by' => $guru->id]
            );
        }

        $this->afterAction('Siswa terpilih berhasil disetujui.');
    }

    /** Reject bulk */
    public function rejectSelected(): void
    {
        $ids = array_map('intval', $this->checked);
        if (empty($ids)) return;

        $guru = Auth::user();

        $targetIds = User::whereIn('id', $ids)
            ->where('role', 'siswa')
            ->where('sekolah_id', $guru->sekolah_id)
            ->pluck('id');

        User::whereIn('id', $targetIds)->update([
            'is_validated' => false,
            'validated_at' => null,
        ]);

        foreach ($targetIds as $uid) {
            ParticipantValidation::updateOrCreate(
                ['user_id' => $uid],
                ['status' => 'invalid', 'validated_by' => $guru->id]
            );
        }

        $this->afterAction('Siswa terpilih ditolak.');
    }

    /** Approve satu baris */
    public function approveOne(int $id): void
    {
        $guru = Auth::user();

        $s = User::whereKey($id)
            ->where('role', 'siswa')
            ->where('sekolah_id', $guru->sekolah_id)
            ->firstOrFail();

        $s->forceFill([
            'is_validated' => true,
            'validated_at' => now(),
        ])->save();

        ParticipantValidation::updateOrCreate(
            ['user_id' => $s->id],
            ['status' => 'valid', 'validated_by' => $guru->id]
        );

        $this->afterAction("{$s->name} disetujui.");
    }

    /** Reject satu baris */
    public function rejectOne(int $id): void
    {
        $guru = Auth::user();

        $s = User::whereKey($id)
            ->where('role', 'siswa')
            ->where('sekolah_id', $guru->sekolah_id)
            ->firstOrFail();

        $s->forceFill([
            'is_validated' => false,
            'validated_at' => null,
        ])->save();

        ParticipantValidation::updateOrCreate(
            ['user_id' => $s->id],
            ['status' => 'invalid', 'validated_by' => $guru->id]
        );

        $this->afterAction("{$s->name} ditolak.");
    }

    /** Sapu bersih state setelah aksi */
    protected function afterAction(string $message): void
    {
        $this->checked = [];
        $this->selectAllPage = false;
        $this->resetPage();
        session()->flash('ok', $message);
        $this->dispatch('$refresh');
    }

    /** Daftar kelas (distinct) untuk filter */
    protected function kelasList(): array
    {
        $guru = Auth::user();

        return User::query()
            ->where('role', 'siswa')
            ->where('sekolah_id', $guru->sekolah_id)
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->filter() // buang null/kosong
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.guru.validate-students', [
            'students'  => $this->baseQuery()->paginate($this->perPage),
            'kelasList' => $this->kelasList(),
        ]);
    }
}
