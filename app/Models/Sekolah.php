<?php
// app/Models/Sekolah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $fillable = ['nama', 'npsn', 'alamat'];
}
