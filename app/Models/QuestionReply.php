<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionReply extends Model
{
    protected $fillable = [
        'thread_id',
        'user_id',
        'isi',
        'is_solution',
    ];

    public function thread()
    {
        return $this->belongsTo(QuestionThread::class, 'thread_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
