<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Questions\QuestionResource;
use Filament\Resources\RelationManagers\RelationManager;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $relatedResource = QuestionResource::class;


    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('teks')
                ->label('Teks Jawaban')
                ->required(),

            Toggle::make('benar')
                ->label('Jawaban Benar')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teks')
                    ->label('Jawaban')
                    ->wrap()
                    ->searchable(),

                IconColumn::make('benar')
                    ->label('Benar')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Jawaban'),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ]);
    }
}
