<?php

namespace App\Policies;

use App\Models\User;
use App\Models\QuestionReply;

class QuestionReplyPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->role !== 'siswa';
    }
    public function view(User $u, QuestionReply $r): bool
    {
        return $u->role !== 'siswa';
    }
    public function create(User $u): bool
    {
        return $u->role !== 'siswa';
    }
    public function update(User $u, QuestionReply $r): bool
    {
        return $u->role !== 'siswa';
    }
    public function delete(User $u, QuestionReply $r): bool
    {
        return $u->role !== 'siswa';
    }
}
