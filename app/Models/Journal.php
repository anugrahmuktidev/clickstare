<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
    ];

    public function getThumbnailUrlAttribute(): string
    {
        return $this->file_path
            ? asset('images/pdf-placeholder.png')
            : asset('images/pdf-placeholder.png');
    }
}
