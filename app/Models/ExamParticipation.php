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
        'pocket_money_range',
        'uses_electric_smoke',
        'uses_conventional_smoke',
        'uses_both_smoke_types',
        'pretest_completed_at',
        'sikap_completed_at',
        'knowledge_test_completed_at',
        'knowledge_test_post_completed_at',
        'sikap_post_completed_at',
        'video_watched_at',
        'posttest_completed_at',
    ];

    protected $casts = [
        'uses_electric_smoke' => 'boolean',
        'uses_conventional_smoke' => 'boolean',
        'uses_both_smoke_types' => 'boolean',
        'pretest_completed_at'  => 'datetime',
        'sikap_completed_at'    => 'datetime',
        'knowledge_test_completed_at' => 'datetime',
        'knowledge_test_post_completed_at' => 'datetime',
        'sikap_post_completed_at' => 'datetime',
        'video_watched_at'      => 'datetime',
        'posttest_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
