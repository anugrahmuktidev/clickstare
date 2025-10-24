<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            FileUpload::make('image_path')
                ->label('Gambar')
                ->disk('public')
                ->directory('hero-slides')
                ->visibility('public')
                ->image()
                ->preserveFilenames()
                ->required(),

            Grid::make(2)->schema([
                TextInput::make('title')
                    ->label('Judul')
                    ->maxLength(150),

                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
            ])->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }
}
