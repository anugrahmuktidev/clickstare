<?php

namespace App\Filament\Resources\Modules\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextInput::make('judul')
                ->label('Judul Modul')
                ->required()
                ->maxLength(200),

            Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->rows(3),

            FileUpload::make('file_path')
                ->label('File Modul (PDF)')
                ->disk('public')
                ->directory('modules')
                ->visibility('public')
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(51200)
                ->required()
                ->preserveFilenames()
                ->openable()
                ->downloadable(),
        ]);
    }
}
