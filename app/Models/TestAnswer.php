<?php

namespace App\Models;

use App\Models\Option;
use App\Models\Question;
use App\Models\TestAttempt;
use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    protected $fillable = ['test_attempt_id', 'question_id', 'option_id', 'is_correct'];

    public function attempt()
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
