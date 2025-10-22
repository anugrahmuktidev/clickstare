<?php

namespace App\Filament\Resources\Options\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class OptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Grouping per soal: judul grup pakai NOMOR, bukan teks
            ->groups([
                Group::make('question_id')->label('Nomor')
                    ->collapsible()
                    ->getTitleFromRecordUsing(
                        fn($record) => ($record->question?->nomor !== null)
                            ?  $record->question->nomor . '. ' . $record->question->teks
                            : '—'
                    ),
            ])
            ->defaultGroup('question_id')
            ->columns([
                // (Opsional) tampilkan kolom nomor kecil di tiap baris:
                TextColumn::make('question.nomor')
                    ->label('No.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Hilangkan teks pertanyaan (sudah dikomentari)
                // TextColumn::make('question.teks')
                //     ->label('Pertanyaan')
                //     ->wrap()
                //     ->limit(60)
                //     ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('teks')
                    ->label('Jawaban')
                    ->wrap()
                    ->searchable(),

                IconColumn::make('benar')
                    ->label('Benar')
                    ->boolean()
                    ->sortable(),
            ])->filters([
                // Filter berdasarkan TIPE dari Question yang terkait
                SelectFilter::make('tipe')
                    ->label('Tipe Soal')
                    ->options([
                        'pre'  => 'PRE',
                        'post' => 'POST',
                    ])
                    ->query(function ($query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('question', fn($q) => $q->where('tipe', $data['value']));
                        }
                    })
                    ->indicator('Tipe'),

                // (Opsional) filter hanya jawaban benar/salah
                TernaryFilter::make('benar')
                    ->label('Status Jawaban')
                    ->placeholder('Semua')
                    ->trueLabel('Hanya Benar')
                    ->falseLabel('Hanya Salah')
                    ->queries(
                        true: fn($query) => $query->where('benar', true),
                        false: fn($query) => $query->where('benar', false),
                        blank: fn($query) => $query
                    )
                    ->indicator('Benar'),
            ])
            ->defaultSort('question_id')
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
