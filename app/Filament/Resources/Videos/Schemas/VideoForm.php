<?php

namespace App\Filament\Resources\Videos\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;

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

            FileUpload::make('path')
                ->label('File Video')
                ->disk('public')                 // storage/app/public
                ->directory('test-upload')            // isi 'path' jadi 'videos/xxx.mp4'
                ->visibility('public')
                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                ->maxSize(512000)                // 500 MB (satuan KB!)
                ->preserveFilenames()
                ->openable()
                ->downloadable(),

            Toggle::make('is_active')
                ->label('Aktifkan sebagai video sesi setelah pretest')
                ->inline(false)
                ->helperText('Hanya satu video yang bisa aktif pada satu waktu. Menandai opsi ini akan menonaktifkan video lain.')
                ->default(false),

        ]);
    }
}
