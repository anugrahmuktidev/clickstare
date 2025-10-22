<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Question extends Model
{
    use HasFactory;

    // cukup satu field: tipe (PRE/POST)
    protected $fillable = ['tipe', 'nomor', 'teks'];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Question $q) {
            if (! in_array($q->tipe, ['pre', 'post'], true)) {
                throw ValidationException::withMessages(['tipe' => 'Tipe harus pre atau post.']);
            }

            // nomor otomatis
            if (empty($q->nomor)) {
                $next = (int) static::where('tipe', $q->tipe)->max('nomor');
                $next = $next ? $next + 1 : 1;

                $q->nomor = $next;
            }
        });
    }
}
