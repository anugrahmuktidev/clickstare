<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property bool $is_validated
 * @method bool isAdmin()
 * @method bool isGuru()
 * @method bool isSiswa()
 * @method bool isValidated()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi mass assignment.
     */
    protected $fillable = [
        'username',
        'role',
        'sekolah_id',
        'name',
        'nisn',
        'nip',
        'jabatan',
        'umur',
        'kelas',
        'jenis_kelamin',
        'pekerjaan_orangtua',
        'alamat',
        'email',
        'password',
        'is_validated',
        'validated_at',
    ];
    /**
     * Sembunyikan kolom sensitif saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut (aktifkan jika kolomnya ada di tabel users).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_validated'      => 'boolean',  // ← aktif kalau kolomnya ada
        'validated_at'      => 'datetime', // ← aktif kalau kolomnya ada
    ];

    /* ===========================
     | Relasi
     |===========================*/
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function pertanyaan()
    {
        return $this->hasMany(QuestionUser::class);
    }

    /* ===========================
     | Helper Role
     |===========================*/
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /* ===========================
     | Helper Validasi Akun
     |===========================*/
    public function isValidated(): bool
    {
        // Jika kolom is_validated ada → kembalikan nilainya, selain itu anggap false.
        return (bool) ($this->is_validated ?? false);
    }

    /* ===========================
     | (Opsional) Scopes cepat
     |===========================*/
    public function scopeAdmins($q)
    {
        return $q->where('role', 'admin');
    }
    public function scopeGurus($q)
    {
        return $q->where('role', 'guru');
    }
    public function scopeSiswa($q)
    {
        return $q->where('role', 'siswa');
    }
}
