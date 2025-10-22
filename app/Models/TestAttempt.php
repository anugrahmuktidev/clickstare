<?php

namespace App\Models;

use App\Models\User;
use App\Models\TestAnswer;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'tipe',
        'total_soal',
        'total_benar',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function answers()
    {
        return $this->hasMany(TestAnswer::class);
    }
}
