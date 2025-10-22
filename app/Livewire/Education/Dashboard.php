<?php

namespace App\Livewire\Education;

use App\Models\Faq;
use App\Models\Video;
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

        $videos  = Video::latest()->take(20)->get(); // tampilkan 20 terbaru
        $faqs    = Faq::orderBy('id')->get();
        $threads = QuestionThread::with(['asker', 'replies.user', 'solution'])
            ->where('sekolah_id', $user->sekolah_id)
            ->latest()->paginate(10);

        return view('livewire.education.dashboard', compact('videos', 'faqs', 'threads'));
    }
}
