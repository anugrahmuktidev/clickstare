<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'cta_label',
        'cta_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];
}
