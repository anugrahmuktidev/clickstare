<?php

namespace App\Filament\Resources\Videos\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;


class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable(),

                TextColumn::make('path')
                    ->label('File')
                    ->wrap()
                    ->formatStateUsing(fn($state) => $state ? '/storage/' . $state : '—'),

                IconColumn::make('is_after_pretest')
                    ->label('Setelah Pretest')
                    ->boolean(),

                // Kolom URL khusus siswa — bisa di-copy
                TextColumn::make('student_url')
                    ->label('URL Siswa')
                    ->state(fn($record) => route('education.watch', $record->id))
                    ->copyable()                      // klik icon copy
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])

            ->recordActions([
                // 🔎 Pratinjau video di modal (admin tetap di panel)
                Action::make('preview')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Pratinjau Video')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn($record) => view('filament.videos.preview', ['record' => $record])),

                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()->label('Hapus'),
            ])            // matikan semua bulk action selain hapus (kalau mau)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang dipilih'),
                ]),
            ]);
    }
}
