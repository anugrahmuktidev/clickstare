<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'path', 'thumbnail_path', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        // Hapus file saat record dihapus
        static::deleting(function (Video $video) {
            if ($video->path) Storage::disk('public')->delete($video->path);
            if ($video->thumbnail_path) Storage::disk('public')->delete($video->thumbnail_path);
        });

        // Buat thumbnail saat pertama kali dibuat (jika ada path)
        static::created(function (Video $video) {
            if ($video->path) {
                $video->generateThumbnail(); // sinkron
            }
        });

        // Jika file video diganti, regenerate thumbnail
        static::updated(function (Video $video) {
            if ($video->wasChanged('path') && $video->path) {
                $video->generateThumbnail(); // sinkron
            }
        });

        static::saved(function (Video $video) {
            if ($video->is_active) {
                static::whereKeyNot($video->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * Generate thumbnail menggunakan ffmpeg (tanpa queue).
     * Ambil frame di detik ke-1, simpan ke public/videos/thumbs/{nama}.jpg
     */
    public function generateThumbnail(): void
    {
        try {
            $disk = Storage::disk('public');

            // Pastikan file video ada
            if (! $this->path || ! $disk->exists($this->path)) {
                return;
            }

            // Tentukan nama & lokasi thumbnail
            $filename   = pathinfo($this->path, PATHINFO_FILENAME) . '.jpg';
            $thumbRel   = 'videos/thumbs/' . $filename;
            $videoAbs   = $disk->path($this->path);
            $thumbAbs   = $disk->path($thumbRel);

            // Pastikan folder thumbs ada
            if (! $disk->exists('videos/thumbs')) {
                @mkdir(dirname($thumbAbs), 0775, true);
            }

            // Perintah ffmpeg: ambil frame di 1 detik pertama (ubah -ss sesuai perlu)
            $cmd = sprintf(
                'ffmpeg -hide_banner -loglevel error -ss 00:00:01 -i %s -frames:v 1 -q:v 2 %s -y',
                escapeshellarg($videoAbs),
                escapeshellarg($thumbAbs)
            );

            @exec($cmd, $out, $exitCode);

            if ($exitCode === 0 && $disk->exists($thumbRel)) {
                // update kolom thumbnail_path
                $this->forceFill(['thumbnail_path' => $thumbRel])->saveQuietly();
            }
        } catch (\Throwable $e) {
            // diamkan agar tidak mengganggu flow simpan
            // kalau mau debug: \Log::warning('Thumb fail: '.$e->getMessage());
        }
    }

    /** URL siap pakai di Blade */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->path ? Storage::url($this->path) : null;
    }
}
