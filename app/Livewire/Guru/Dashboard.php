<?php
// app/Livewire/Guru/Dashboard.php
namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Sekolah;
use App\Models\User;
use App\Models\Video;
use App\Models\QuestionThread;
use App\Models\QuestionReply;
use App\Support\KnowledgeTestSummary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;


#[Layout('layouts.guest')]

class Dashboard extends Component
{
    use WithPagination;

    public int $sekolahId;
    public array $reply = []; // reply[thread_id] => teks
    public array $asSolution = []; // asSolution[thread_id] => bool
    public bool $isPretestEnabled = false;
    public bool $isPosttestEnabled = false;

    // ringkasan
    public int $totalSiswa = 0;
    public int $totalSiswaValid = 0;
    public float $avgPre = 0.0;
    public float $avgPost = 0.0;
    public int $newThreads = 0;
    public int $unansweredThreads = 0;

    public function mount(): void
    {
        $u = Auth::user();
        $this->sekolahId = (int) $u->sekolah_id;
        $this->isPretestEnabled = (bool) ($u->sekolah?->is_pretest_enabled ?? false);
        $this->isPosttestEnabled = (bool) ($u->sekolah?->is_posttest_enabled ?? false);

        // ringkasan cepat
        $this->totalSiswa = User::where('role', 'siswa')
            ->where('sekolah_id', $this->sekolahId)->count();

        $this->totalSiswaValid = User::where('role', 'siswa')
            ->where('sekolah_id', $this->sekolahId)
            ->where('is_validated', true)->count();

        $this->refreshScoreSummary();

        $this->newThreads = QuestionThread::where('sekolah_id', $this->sekolahId)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $this->unansweredThreads = QuestionThread::where('sekolah_id', $this->sekolahId)
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'closed');
            })
            ->count();
    }

    public function togglePretestAccess(): void
    {
        $sekolah = Sekolah::query()->findOrFail($this->sekolahId);
        $enabled = ! (bool) $sekolah->is_pretest_enabled;

        $sekolah->update([
            'is_pretest_enabled' => $enabled,
        ]);

        $this->isPretestEnabled = $enabled;

        session()->flash(
            'exam_ok',
            $enabled
                ? 'Akses pretest berhasil diaktifkan untuk sekolah Anda.'
                : 'Akses pretest berhasil dinonaktifkan untuk sekolah Anda.'
        );
    }

    public function togglePosttestAccess(): void
    {
        $sekolah = Sekolah::query()->findOrFail($this->sekolahId);
        $enabled = ! (bool) $sekolah->is_posttest_enabled;

        $sekolah->update([
            'is_posttest_enabled' => $enabled,
        ]);

        $this->isPosttestEnabled = $enabled;

        session()->flash(
            'exam_ok',
            $enabled
                ? 'Akses posttest berhasil diaktifkan untuk sekolah Anda.'
                : 'Akses posttest berhasil dinonaktifkan untuk sekolah Anda.'
        );
    }

    public function answer(int $threadId): void
    {
        $this->validate([
            "reply.$threadId" => ['required', 'string', 'min:3', 'max:2000'],
        ], [], ['reply.' . $threadId => 'jawaban']);

        $thread = QuestionThread::where('id', $threadId)
            ->where('sekolah_id', $this->sekolahId)
            ->firstOrFail();

        // simpan balasan
        $text = Str::of($this->reply[$threadId])->trim()->toString();
        $reply = QuestionReply::create([
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(), // guru yang login
            'isi'       => $text,
            'is_solution' => false,
        ]);

        // Jika centang sebagai solusi, tandai dan tutup thread
        if (!empty($this->asSolution[$threadId])) {
            QuestionReply::where('thread_id', $thread->id)
                ->where('is_solution', true)
                ->update(['is_solution' => false]);

            $reply->update(['is_solution' => true]);
            $thread->update(['status' => 'closed', 'solved_at' => now()]);

            session()->flash('ok', 'Jawaban ditandai sebagai solusi dan diskusi ditutup.');
        } else {
            session()->flash('ok', 'Jawaban terkirim.');
        }

        // bersihkan form
        $this->reply[$threadId] = '';
        $this->asSolution[$threadId] = false;
    }

    public function toggleThread(int $threadId): void
    {
        $thread = QuestionThread::where('id', $threadId)
            ->where('sekolah_id', $this->sekolahId)
            ->firstOrFail();

        $newStatus = $thread->status === 'closed' ? 'open' : 'closed';

        $payload = ['status' => $newStatus];
        if ($newStatus === 'open') {
            $payload['solved_at'] = null;
        } else {
            $payload['solved_at'] = $thread->solved_at ?? now();
        }

        $thread->update($payload);

        session()->flash('ok', $newStatus === 'closed' ? 'Diskusi ditutup.' : 'Diskusi dibuka kembali.');
    }

    public function markSolution(int $replyId): void
    {
        $reply  = QuestionReply::findOrFail($replyId);
        $thread = $reply->thread;

        // pastikan thread milik sekolah guru
        if ((int) $thread->sekolah_id !== $this->sekolahId) {
            abort(403);
        }

        if (Gate::denies('resolve-thread', $thread)) {
            abort(403);
        }

        QuestionReply::where('thread_id', $thread->id)
            ->where('is_solution', true)
            ->update(['is_solution' => false]);

        $reply->update(['is_solution' => true]);
        $thread->update(['status' => 'closed', 'solved_at' => now()]);

        session()->flash('ok', 'Balasan ditandai sebagai solusi dan diskusi ditutup.');
    }

    public function render()
    {
        $this->refreshScoreSummary();

        $this->newThreads = QuestionThread::where('sekolah_id', $this->sekolahId)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $this->unansweredThreads = QuestionThread::where('sekolah_id', $this->sekolahId)
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'closed');
            })
            ->count();

        // daftar siswa valid + progress (10 data per halaman)
        $students = User::where('role', 'siswa')
            ->where('sekolah_id', $this->sekolahId)
            ->where('is_validated', true)
            ->orderBy('name')
            ->with(['knowledgeAnswers.question:id,correct_answer']) // cepat
            ->paginate(10, ['*'], 'studentsPage');

        $students->getCollection()->transform(function (User $student) {
            $pre = KnowledgeTestSummary::summarizeForUser($student, 'pre');
            $post = KnowledgeTestSummary::summarizeForUser($student, 'post');

            $student->pre_score = $pre['score'] ?? null;
            $student->post_score = $post['score'] ?? null;
            $student->last_answered_at = $post['answered_at'] ?? $pre['answered_at'] ?? null;

            return $student;
        });

        // thread sekolah (5 terbaru) + replies
        $threads = QuestionThread::with([
            'asker:id,name',
            'solution.user:id,name',
            'replies' => fn($q) => $q->with('user:id,name')->latest(),
        ])
            ->where('sekolah_id', $this->sekolahId)
            ->latest()
            ->paginate(5);

        // video edukasi (8)
        $videos = Video::latest()->take(8)->get();

        return view('livewire.guru.dashboard', compact('students', 'threads', 'videos'));
    }

    protected function refreshScoreSummary(): void
    {
        $students = User::query()
            ->where('role', 'siswa')
            ->where('sekolah_id', $this->sekolahId)
            ->where('is_validated', true)
            ->with(['knowledgeAnswers.question:id,correct_answer'])
            ->get();

        $preScores = [];
        $postScores = [];

        foreach ($students as $student) {
            $pre = KnowledgeTestSummary::summarizeForUser($student, 'pre');
            $post = KnowledgeTestSummary::summarizeForUser($student, 'post');

            if ($pre) {
                $preScores[] = $pre['score'];
            }

            if ($post) {
                $postScores[] = $post['score'];
            }
        }

        $this->avgPre = round(collect($preScores)->avg() ?? 0, 1);
        $this->avgPost = round(collect($postScores)->avg() ?? 0, 1);
    }
}
