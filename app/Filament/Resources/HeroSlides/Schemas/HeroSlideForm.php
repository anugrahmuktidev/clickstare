<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Gambar Slider')
                ->schema([
                    FileUpload::make('image_path')
                        ->label('Gambar')
                        ->disk('public')
                        ->directory('hero-slides')
                        ->visibility('public')
                        ->image()
                        ->preserveFilenames()
                        ->required(),
                ]),

            Section::make('Informasi Tambahan')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->maxLength(150),

                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('cta_label')
                            ->label('Label Tombol')
                            ->maxLength(60),

                        TextInput::make('cta_url')
                            ->label('URL Tombol')
                            ->maxLength(255)
                            ->url()
                            ->placeholder('https://contoh.com'),
                    ]),

                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ]),
        ]);
    }
}
