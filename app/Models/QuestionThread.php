<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionThread extends Model
{
    protected $fillable = [
        'user_id',
        'sekolah_id',
        'judul',
        'isi',
        'status',
        'solved_at',
        'status'
    ];

    public function asker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(QuestionReply::class, 'thread_id');
    }

    public function solution()
    {
        return $this->hasOne(QuestionReply::class, 'thread_id')->where('is_solution', true);
    }
}
