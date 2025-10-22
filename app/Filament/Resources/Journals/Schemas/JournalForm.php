<?php

namespace App\Filament\Resources\Journals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class JournalForm
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

            FileUpload::make('file_path')
                ->label('File PDF')
                ->disk('public')
                ->directory('journals')
                ->visibility('public')
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(51200) // 50 MB dalam kilobyte.
                ->required()
                ->preserveFilenames()
                ->openable()
                ->downloadable(),
        ]);
    }
}
