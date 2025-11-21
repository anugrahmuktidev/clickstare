<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttitudeAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attitude_question_id',
        'user_id',
        'stage',
        'value',
    ];

    public function question()
    {
        return $this->belongsTo(AttitudeQuestion::class, 'attitude_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
