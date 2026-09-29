<?php

namespace App\Filament\Resources\Videos\Schemas;

use Illuminate\Support\Facades\Storage;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextInput::make('judul')
                ->label('Judul')
                ->required()
                ->maxLength(200),

            Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->rows(3),

            Toggle::make('is_after_pretest')
                ->label('Tampilkan khusus setelah pretest')
                ->helperText('Video ini akan diarahkan otomatis setelah siswa menyelesaikan pretest. Posttest baru terbuka setelah video selesai ditonton.')
                ->default(false)
                ->inline(false),

            Select::make('existing_video_path')
                ->label('Pilih file video yang sudah diupload')
                ->helperText('Upload file lewat File Manager/FTP ke storage/app/public/videos, lalu pilih file di sini. Gunakan ini jika upload dari form gagal di cPanel.')
                ->options(fn () => collect(Storage::disk('public')->files('videos'))
                    ->filter(fn (string $path): bool => in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'qt'], true))
                    ->sort()
                    ->mapWithKeys(fn (string $path): array => [$path => $path])
                    ->all())
                ->searchable()
                ->preload()
                ->placeholder('Tidak memilih file existing'),

            TextInput::make('manual_video_path')
                ->label('Path / URL video manual')
                ->helperText('Alternatif untuk cPanel: upload file ke public/storage/videos, lalu isi contoh: videos/gabungan.mov. Field ini tidak memakai proses upload Livewire.')
                ->placeholder('videos/gabungan.mov')
                ->maxLength(500),

            FileUpload::make('path')
                ->label('File Video')
                ->disk('public')                 // storage/app/public
                ->directory('videos')            // isi 'path' jadi 'videos/xxx.mp4'
                ->visibility('public')
                ->acceptedFileTypes(['video/*', 'video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-quicktime', 'application/octet-stream'])
                ->maxSize(512000)                // 500 MB (satuan KB!)
                ->preserveFilenames()
                ->openable()
                ->downloadable(),
        ]);
    }
}
