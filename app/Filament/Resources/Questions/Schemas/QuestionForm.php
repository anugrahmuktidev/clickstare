<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipe')
                    ->label('Tipe Soal')
                    ->options(['pre' => 'PRE', 'post' => 'POST'])
                    ->required(),

                TextInput::make('nomor')
                    ->label('Nomor')
                    ->readOnly()
                    ->dehydrated(false)
                    ->visibleOn('edit'),

                TextInput::make('teks')
                    ->label('Teks Pertanyaan')
                    ->required()
                    ->maxLength(500),
            ]);
    }
}
