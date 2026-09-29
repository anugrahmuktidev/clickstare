<?php

namespace App\Filament\Resources\TestAttempts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TestAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Peserta')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('tipe')
                    ->label('Jenis Tes')
                    ->options([
                        'pre' => 'Pretest',
                        'post' => 'Posttest',
                    ])
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('total_soal')
                    ->label('Total Soal')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('total_benar')
                    ->label('Jumlah Benar')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(fn ($record) => $record?->total_soal ?? 999999)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                        $totalSoal = (int) ($get('total_soal') ?? 0);
                        $totalBenar = (int) ($state ?? 0);

                        $set('score', $totalSoal > 0 ? (int) round(($totalBenar / $totalSoal) * 100) : 0);
                    })
                    ->required(),
                TextInput::make('score')
                    ->label('Score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
            ]);
    }
}
