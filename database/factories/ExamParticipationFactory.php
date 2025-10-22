<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamParticipation extends Model
{
    protected $fillable = [
        'user_id',
        'current_step',
        'pretest_completed_at',
        'video_watched_at',
        'posttest_completed_at',
    ];

    protected $casts = [
        'pretest_completed_at'  => 'datetime',
        'video_watched_at'      => 'datetime',
        'posttest_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
