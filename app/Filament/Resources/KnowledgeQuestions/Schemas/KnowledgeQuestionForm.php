<?php

namespace App\Filament\Resources\KnowledgeQuestions\Schemas;

use App\Models\KnowledgeQuestion;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KnowledgeQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('teks')
                    ->label('Pertanyaan')
                    ->rows(3)
                    ->required()
                    ->maxLength(1000),

                TextInput::make('sort_order')
                    ->label('Urutan Tampil')
                    ->numeric()
                    ->minValue(1)
                    ->default(function (?KnowledgeQuestion $record) {
                        if ($record) {
                            return $record->sort_order;
                        }

                        $last = (int) (KnowledgeQuestion::max('sort_order') ?? 0);
                        return $last + 1;
                    }),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
