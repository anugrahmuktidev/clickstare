<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'sekolah_id',
        'nama',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function participations()
    {
        return $this->hasMany(ExamParticipation::class);
    }
}
