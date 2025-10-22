<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParticipantValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'validated_by',
        'catatan',
    ]; // status: pending|approved|rejected

    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
