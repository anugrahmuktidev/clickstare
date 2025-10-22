<?php

namespace App\Filament\Resources\Videos\Tables;

use App\Models\Video;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;


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

                IconColumn::make('is_active')
                    ->label('Aktif?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-minus-circle'),

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

                Action::make('setActive')
                    ->label('Jadikan Aktif')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Video $record) => ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (Video $record) {
                        $record->forceFill(['is_active' => true])->save();
                    }),

                // EditAction::make()->label('Ubah'),

                DeleteAction::make()->label('Hapus'),
            ])            // matikan semua bulk action selain hapus (kalau mau)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang dipilih'),
                ]),
            ]);
    }
}
