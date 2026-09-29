<?php
// app/Models/Sekolah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $fillable = [
        'nama',
        'npsn',
        'alamat',
        'is_pretest_enabled',
        'is_posttest_enabled',
    ];

    protected $casts = [
        'is_pretest_enabled' => 'boolean',
        'is_posttest_enabled' => 'boolean',
    ];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function activeExamSessions()
    {
        return $this->examSessions()->where('is_active', true);
    }
}
