<?php

namespace App\Filament\Resources\HeroSlides\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HeroSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->disk('public')
                    ->height('60px')
                    ->limitedRemainingText(false),

                TextColumn::make('title')
                    ->label('Judul')
                    ->wrap()
                    ->placeholder('—'),

                TextColumn::make('cta_label')
                    ->label('Label Tombol')
                    ->placeholder('—'),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->since(),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang dipilih'),
                ]),
            ]);
    }
}
