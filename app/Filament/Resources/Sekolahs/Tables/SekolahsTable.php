<?php

namespace App\Filament\Resources\Sekolahs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SekolahsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('alamat')
                    ->searchable(),
                TextColumn::make('classes_count')
                    ->label('Kelas')
                    ->counts('classes'),
                TextColumn::make('exam_sessions_count')
                    ->label('Sesi')
                    ->counts('examSessions'),
                ToggleColumn::make('is_pretest_enabled')
                    ->label('Pretest')
                    ->onColor('success')
                    ->offColor('gray')
                    ->tooltip(fn($state): string => $state ? 'Pretest ditampilkan untuk sekolah ini.' : 'Pretest disembunyikan untuk sekolah ini.'),
                ToggleColumn::make('is_posttest_enabled')
                    ->label('Posttest')
                    ->onColor('success')
                    ->offColor('gray')
                    ->tooltip(fn($state): string => $state ? 'Posttest ditampilkan untuk sekolah ini.' : 'Posttest disembunyikan untuk sekolah ini.'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
