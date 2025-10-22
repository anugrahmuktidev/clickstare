<?php

namespace App\Filament\Resources\Options\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;

class OptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // ⬇️ GANTI BAGIAN INI
            Select::make('question_id')
                ->label('Pertanyaan')
                ->relationship('question', 'teks')
                ->getOptionLabelFromRecordUsing(
                    fn($record) =>
                    '[' . strtoupper($record->tipe) . '] No. ' . $record->nomor . ' — ' . $record->teks
                )
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('teks')
                ->label('Teks Jawaban')
                ->required()
                ->maxLength(255),

            Toggle::make('benar')
                ->label('Jawaban Benar')
                ->inline(false)
                ->default(false),
        ]);
    }
}
