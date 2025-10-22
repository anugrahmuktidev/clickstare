<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamParticipation extends Model
{
    protected $fillable = [
        'user_id',
        'current_step',
        'pretest_completed_at',
        'video_completed_at',
        'posttest_completed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
