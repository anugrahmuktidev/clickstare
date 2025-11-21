<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamParticipation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_step',
        'pretest_completed_at',
        'sikap_completed_at',
        'sikap_post_completed_at',
        'video_watched_at',
        'posttest_completed_at',
    ];

    protected $casts = [
        'pretest_completed_at'  => 'datetime',
        'sikap_completed_at'    => 'datetime',
        'sikap_post_completed_at' => 'datetime',
        'video_watched_at'      => 'datetime',
        'posttest_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
