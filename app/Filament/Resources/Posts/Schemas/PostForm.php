<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextInput::make('judul')
                ->label('Judul')
                ->required()
                ->maxLength(200),

            FileUpload::make('gambar_path')
                ->label('Gambar Sampul')
                ->disk('public')
                ->directory('posts')
                ->visibility('public')
                ->image()
                ->maxSize(5120) // 5 MB
                ->preserveFilenames()
                ->openable()
                ->downloadable(),

            RichEditor::make('konten')
                ->label('Konten')
                ->required()
                ->columnSpanFull(),
        ]);
    }
}
