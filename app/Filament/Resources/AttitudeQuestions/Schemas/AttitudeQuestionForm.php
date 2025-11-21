<?php

namespace App\Filament\Resources\AttitudeQuestions\Schemas;

use App\Models\AttitudeQuestion;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttitudeQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('teks')
                    ->label('Pernyataan')
                    ->rows(3)
                    ->required()
                    ->maxLength(1000),

                TextInput::make('sort_order')
                    ->label('Urutan Tampil')
                    ->numeric()
                    ->minValue(1)
                    ->default(function (?AttitudeQuestion $record) {
                        if ($record) {
                            return $record->sort_order;
                        }

                        $last = (int) (AttitudeQuestion::max('sort_order') ?? 0);
                        return $last + 1;
                    }),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
