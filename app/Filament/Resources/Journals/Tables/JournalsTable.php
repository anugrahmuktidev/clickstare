<?php

namespace App\Filament\Resources\Journals\Tables;

use App\Models\Journal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class JournalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn(?string $state) => $state ? Storage::url($state) : '—')
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Pratinjau Jurnal')
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn(Journal $record) => view('filament.journals.preview', ['record' => $record]))
                    ->visible(fn(Journal $record) => filled($record->file_path)),

                Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(Journal $record) => Storage::url($record->file_path), shouldOpenInNewTab: true)
                    ->visible(fn(Journal $record) => filled($record->file_path)),

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
