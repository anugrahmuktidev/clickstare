<?php

namespace App\Filament\Resources\QuestionThreads\Tables;

use App\Filament\Resources\QuestionThreads\QuestionThreadResource;
use App\Models\QuestionThread;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;

class QuestionThreadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')->searchable()->wrap()->limit(60)
                    ->url(
                        fn(QuestionThread $record) =>
                        QuestionThreadResource::getUrl('view', ['record' => $record])
                    ),

                Tables\Columns\TextColumn::make('asker.name')
                    ->label('Penanya')->sortable()->default('—'),

                Tables\Columns\TextColumn::make('sekolah.nama')
                    ->label('Sekolah')->sortable()->toggleable()->default('—'),

                Tables\Columns\TextColumn::make('replies_count')
                    ->counts('replies')->label('Balasan')->sortable(),

                Tables\Columns\IconColumn::make('has_solution')
                    ->label('Solusi')->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()->label('Status')
                    ->color(fn(string $state): string => $state === 'open' ? 'warning' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->since()->label('Dibuat')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['open' => 'Open', 'closed' => 'Closed']),
                Tables\Filters\SelectFilter::make('sekolah_id')->label('Sekolah')->relationship('sekolah', 'nama'),
            ])
            ->recordActions([
                Action::make('lihat')
                    ->label('Lihat & Balas')
                    ->icon('heroicon-o-eye')
                    ->url(
                        fn(QuestionThread $record) =>
                        QuestionThreadResource::getUrl('view', ['record' => $record])
                    ),
            ])
            ->recordUrl(null);
    }
}
