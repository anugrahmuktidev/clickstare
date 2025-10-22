<?php

namespace App\Filament\Resources\TestAttempts\Tables;

use App\Filament\Resources\TestAttempts\TestAttemptResource;
use App\Filament\Exports\TestAttemptExporter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class TestAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Filters: pilih jenis tes (Pre/Post)
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Jenis Tes')
                    ->options([
                        'pre' => 'Pretest',
                        'post' => 'Posttest',
                    ])
                    ->attribute('tipe'),

                // Filter per Sekolah (mengambil dari relasi user.sekolah)
                SelectFilter::make('sekolah')
                    ->label('Sekolah')
                    ->relationship('user.sekolah', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->columns([
                TextColumn::make('user.name')   // 👈 ambil dari relasi
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tipe')
                    ->label('Jenis Tes'),

                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('summary')
                    ->label('Benar/Total')
                    ->state(function ($record) {
                        $totalBenar = $record->total_benar;
                        $totalSoal  = $record->total_soal;
                        if ($totalBenar === null || $totalSoal === null) {
                            $totalSoal  = $record->answers()->count();
                            $totalBenar = $record->answers()->where('is_correct', true)->count();
                        }
                        return (string) ((int) ($totalBenar ?? 0)) . '/' . (string) ((int) ($totalSoal ?? 0));
                    }),

                // Tampilkan total benar dari relasi (jika tersedia di kolom)
                TextColumn::make('total_benar')
                    ->label('Benar')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_soal')
                    ->label('Total Soal')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                // // Link ke halaman detail (view) untuk melihat jawaban per-soal
                // TextColumn::make('detail')
                //     ->label('Detail')
                //     ->formatStateUsing(fn (): string => 'Lihat')
                //     ->url(fn ($record) => TestAttemptResource::getUrl('view', ['record' => $record->getKey()]))
                //     ->color('primary')
                //     ->weight('bold'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                // gunakan kolom link di atas sebagai akses detail
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(TestAttemptExporter::class)
                    ->label('Export Excel')
                    ->formats([ExportFormat::Xlsx])
                    ->fileName(fn () => 'hasil-tes-' . now()->format('Ymd-His'))
                    ->authGuard('web')
                    ->columnMapping(false)
                    ->modifyQueryUsing(function ($query, array $options) {
                        if (! empty($options['sekolah_id'])) {
                            $query->whereHas('user', function ($q) use ($options) {
                                $q->where('sekolah_id', $options['sekolah_id']);
                            });
                        }
                        $jenis = $options['test_type'] ?? 'all';
                        if ($jenis !== 'all') {
                            $query->where('tipe', $jenis);
                        }
                        return $query;
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
