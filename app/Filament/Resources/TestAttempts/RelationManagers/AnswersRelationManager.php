<?php

namespace App\Filament\Resources\TestAttempts\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $title = 'Jawaban Per Soal';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question.nomor')
                    ->label('No')
                    ->sortable(),
                TextColumn::make('question.teks')
                    ->label('Soal')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('option.teks')
                    ->label('Jawaban')
                    ->wrap()
                    ->searchable(),
                IconColumn::make('is_correct')
                    ->label('Benar?')
                    ->boolean(),
            ])
            ->defaultSort('question.nomor');
    }
}

