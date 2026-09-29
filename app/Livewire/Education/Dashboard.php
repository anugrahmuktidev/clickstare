<?php

namespace App\Livewire\Education;

use App\Models\Faq;
use App\Models\ExamParticipation;
use App\Models\Video;
use App\Support\KnowledgeTestSummary;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\QuestionReply;
use App\Models\QuestionThread;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.guest')]
class Dashboard extends Component
{
    use WithPagination;

    // form tanya
    public string $judul = '';
    // public string $isi = '';
    public array $reply = []; // reply[thread_id] = teks

    public ?string $isi = null;   // ← cukup 1 field
    public ?int $selected_exam_session_id = null;

    public function mount(): void
    {
        $this->selected_exam_session_id = session('active_exam_session_id');
    }

    public function selectExamSession(): void
    {
        $user = Auth::user();

        $data = $this->validate([
            'selected_exam_session_id' => ['required', 'exists:exam_sessions,id'],
        ], [], [
            'selected_exam_session_id' => 'sesi test',
        ]);

        $session = $user->sekolah?->activeExamSessions()
            ->whereKey($data['selected_exam_session_id'])
            ->first();

        if (! $session) {
            $this->addError('selected_exam_session_id', 'Sesi test tidak aktif untuk sekolah Anda.');
            return;
        }

        session(['active_exam_session_id' => $session->id]);
        $this->selected_exam_session_id = $session->id;
        session()->flash('ok', 'Sesi test aktif: ' . $session->nama);
    }

    public function ask(): void
    {
        $data = $this->validate([
            'isi' => ['required', 'string', 'min:8', 'max:2000'],
        ], [], [
            'isi' => 'pertanyaan',
        ]);

        // bikin judul otomatis dari isi
        $clean   = Str::of($data['isi'])->stripTags()->squish();               // rapikan
        $judul   = (string) $clean->before('.');                               // ambil kalimat pertama
        $judul   = Str::of($judul ?: $clean)->limit(80, '…');                  // fallback: 80 char pertama

        $u = Auth::user();

        $thread = QuestionThread::create([
            'user_id'     => $u->id,
            'sekolah_id'  => $u->sekolah_id,
            'judul'       => $judul,
            'isi'         => (string) $clean,
            'status'      => 'open',
        ]);

        // Rapikan dan unikkan judul dengan menambahkan sufiks ID thread
        $suffix = ' • #' . $thread->id;
        $newTitle = Str::of($thread->judul)->limit(70 - strlen($suffix), '…') . $suffix;
        $thread->update(['judul' => (string) $newTitle]);

        if (cache()->add('ask-lock:' . $u->id, true, now()->addSeconds(10)) === false) {
            $this->addError('isi', 'Tunggu beberapa detik sebelum mengirim lagi.');
            return;
        }

        $this->reset('isi');
        session()->flash('ok', 'Pertanyaan terkirim. Guru/Admin akan menanggapi secepatnya.');
    }

    // public function ask()
    // {
    //     $this->validate([
    //         'judul' => ['required', 'string', 'max:200'],
    //         'isi'   => ['required', 'string'],
    //     ]);

    //     QuestionThread::create([
    //         'user_id'    => Auth::id(),
    //         'sekolah_id' => Auth::user()->sekolah_id,
    //         'judul'      => $this->judul,
    //         'isi'        => $this->isi,
    //     ]);

    //     $this->reset(['judul', 'isi']);
    //     session()->flash('ok', 'Pertanyaan terkirim.');
    // }

    public function answer(int $threadId): void
    {
        $thread = QuestionThread::findOrFail($threadId);
        $user   = Auth::user();

        if ($thread->status === 'closed') {
            $this->addError("reply.$threadId", 'Diskusi sudah ditutup.');
            return;
        }

        $isAdmin = $user->role === 'admin';
        $isGuru  = $user->role === 'guru'  && (int)$user->sekolah_id === (int)$thread->sekolah_id;
        $isAsker = $user->role === 'siswa' && (int)$user->id === (int)$thread->user_id; // ← pakai user_id

        if (! ($isAdmin || $isGuru || $isAsker)) {
            $this->addError("reply.$threadId", 'Anda tidak boleh membalas diskusi ini.');
            return;
        }

        $this->validate([
            "reply.$threadId" => ['required', 'string', 'min:3'],
        ], [], [
            "reply.$threadId" => 'jawaban',
        ]);

        QuestionReply::create([
            'thread_id' => $thread->id,
            'user_id'            => $user->id,
            'isi'                => trim($this->reply[$threadId]),
        ]);

        $this->reply[$threadId] = '';
        session()->flash('ok', 'Balasan terkirim.');
        $this->dispatch('$refresh');
    }


    public function markSolution(int $replyId)
    {
        $reply  = QuestionReply::findOrFail($replyId);
        $thread = $reply->thread;

        if (Gate::denies('resolve-thread', $thread)) abort(403);

        QuestionReply::where('thread_id', $thread->id)->where('is_solution', true)->update(['is_solution' => false]);
        $reply->update(['is_solution' => true]);
        $thread->update(['status' => 'closed', 'solved_at' => now()]);
    }



    public function render()
    {
        $user = Auth::user();

        $videosQuery = Video::query()->latest();
        $faqs    = Faq::orderBy('id')->get();
        $threads = QuestionThread::with(['asker', 'replies.user', 'solution'])
            ->where('sekolah_id', $user->sekolah_id)
            ->latest()->paginate(10);

        $certificateAttempt = null;
        $showPretestButton = false;
        $showPosttestButton = false;
        $pretestAttempt = null;
        $posttestAttempt = null;
        $canWatchEducationVideo = true;
        $activeExamSessions = collect();
        $selectedExamSession = null;
        $selectedParticipation = null;

        if ($user->isSiswa()) {
            $activeExamSessions = $user->sekolah?->activeExamSessions()->orderBy('nama')->get() ?? collect();
            $selectedExamSession = $activeExamSessions->firstWhere('id', (int) $this->selected_exam_session_id);

            if (! $selectedExamSession && $activeExamSessions->count() === 1) {
                $selectedExamSession = $activeExamSessions->first();
                $this->selected_exam_session_id = $selectedExamSession->id;
                session(['active_exam_session_id' => $selectedExamSession->id]);
            }

            if ($selectedExamSession) {
                $selectedParticipation = ExamParticipation::query()
                    ->where('user_id', $user->id)
                    ->where('exam_session_id', $selectedExamSession->id)
                    ->first();
            }

            if ($selectedParticipation?->pretest_completed_at && ! $selectedParticipation?->video_watched_at) {
                $requiredVideoIds = Video::query()
                    ->where('is_after_pretest', true)
                    ->pluck('id');

                if ($requiredVideoIds->isNotEmpty()) {
                    $videosQuery->whereIn('id', $requiredVideoIds);
                }
            }

            $pretestAttempt = $selectedParticipation?->pretest_completed_at
                ? KnowledgeTestSummary::summarizeForUser($user, 'pre', $selectedExamSession?->id)
                : null;
            $posttestAttempt = $selectedParticipation?->posttest_completed_at
                ? KnowledgeTestSummary::summarizeForUser($user, 'post', $selectedExamSession?->id)
                : null;

            $pretestAttempt = $pretestAttempt ? (object) $pretestAttempt : null;
            $posttestAttempt = $posttestAttempt ? (object) $posttestAttempt : null;

            $showPretestButton = $selectedExamSession !== null
                && (bool) ($user->sekolah?->is_pretest_enabled ?? false)
                && $pretestAttempt === null;
            $showPosttestButton = (bool) ($user->sekolah?->is_posttest_enabled ?? false)
                && $selectedExamSession !== null
                && $pretestAttempt !== null
                && $selectedParticipation?->video_watched_at !== null
                && $posttestAttempt === null;

            $canWatchEducationVideo = $pretestAttempt !== null;

            if ($posttestAttempt) {
                $certificateAttempt = $posttestAttempt;
            }
        }

        $videos = $videosQuery->take(20)->get(); // tampilkan 20 terbaru

        return view('livewire.education.dashboard', [
            'videos' => $videos,
            'faqs' => $faqs,
            'threads' => $threads,
            'certificateAttempt' => $certificateAttempt,
            'showPretestButton' => $showPretestButton,
            'showPosttestButton' => $showPosttestButton,
            'pretestAttempt' => $pretestAttempt,
            'posttestAttempt' => $posttestAttempt,
            'canWatchEducationVideo' => $canWatchEducationVideo,
            'activeExamSessions' => $activeExamSessions,
            'selectedExamSession' => $selectedExamSession,
        ]);
    }
}
