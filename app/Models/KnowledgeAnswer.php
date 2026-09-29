<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_question_id',
        'user_id',
        'exam_session_id',
        'stage',
        'value',
    ];

    public function question()
    {
        return $this->belongsTo(KnowledgeQuestion::class, 'knowledge_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }
}
