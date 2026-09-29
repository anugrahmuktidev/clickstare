<?php

namespace App\Filament\Resources\Videos\Schemas;

use Filament\Schemas\Schema;
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

            FileUpload::make('path')
                ->label('File Video')
                ->disk('public')                 // storage/app/public
                ->directory('videos')            // isi 'path' jadi 'videos/xxx.mp4'
                ->visibility('public')
                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                ->maxSize(512000)                // 500 MB (satuan KB!)
                ->preserveFilenames()
                ->openable()
                ->downloadable(),
        ]);
    }
}
