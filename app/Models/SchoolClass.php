<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = [
        'sekolah_id',
        'nama',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function students()
    {
        return $this->hasMany(User::class, 'school_class_id');
    }
}
