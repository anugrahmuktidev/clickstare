<?php

namespace App\Filament\Resources\TestAttempts\Tables;

use App\Filament\Exports\TestAttemptExporter;
use App\Filament\Resources\TestAttempts\TestAttemptResource;
use App\Models\ExamSession;
use App\Models\SchoolClass;
use App\Models\Sekolah;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Filters: pilih jenis tes (Pre/Post)
            ->filters([
                SelectFilter::make('sekolah_id')
                    ->label('Sekolah')
                    ->options(fn () => Sekolah::query()->orderBy('nama')->pluck('nama', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('user', fn (Builder $query) => $query->where('sekolah_id', $data['value']));
                    }),
                SelectFilter::make('school_class_id')
                    ->label('Kelas')
                    ->options(fn () => SchoolClass::query()
                        ->with('sekolah:id,nama')
                        ->orderBy('nama')
                        ->get()
                        ->mapWithKeys(fn (SchoolClass $class): array => [
                            $class->id => trim(($class->sekolah?->nama ? $class->sekolah->nama . ' - ' : '') . $class->nama),
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('user', fn (Builder $query) => $query->where('school_class_id', $data['value']));
                    }),
                SelectFilter::make('exam_session_id')
                    ->label('Sesi Test')
                    ->options(fn () => ExamSession::query()
                        ->with('sekolah:id,nama')
                        ->orderBy('nama')
                        ->get()
                        ->mapWithKeys(fn (ExamSession $session): array => [
                            $session->id => trim(($session->sekolah?->nama ? $session->sekolah->nama . ' - ' : '') . $session->nama),
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->attribute('exam_session_id'),
                SelectFilter::make('tipe')
                    ->label('Jenis Tes')
                    ->options([
                        'pre' => 'Pretest',
                        'post' => 'Posttest',
                    ])
                    ->attribute('tipe'),
            ])
            ->columns([
                TextColumn::make('user.name')   // 👈 ambil dari relasi
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.sekolah.nama')
                    ->label('Sekolah')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.schoolClass.nama')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('examSession.nama')
                    ->label('Sesi Test')
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
                //
            ])
            ->recordUrl(fn ($record) => TestAttemptResource::getUrl('edit', ['record' => $record]))
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(TestAttemptExporter::class)
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->formats([ExportFormat::Xlsx])
                    ->fileName(fn () => 'hasil-tes-' . now()->format('Ymd-His'))
                    ->authGuard('web')
                    ->columnMapping(false)
                    ->options(function (ExportAction $action): array {
                        $livewire = $action->getLivewire();
                        $state = $livewire?->getTableFilterState('tipe');
                        $value = is_array($state) ? ($state['value'] ?? null) : $state;

                        return [
                            'test_type' => $value ?: 'all',
                        ];
                    })
                    ->modifyQueryUsing(function ($query, array $options) {
                        if (! empty($options['sekolah_id'])) {
                            $query->where('sekolah_id', $options['sekolah_id']);
                        }

                        if (! empty($options['school_class_id'])) {
                            $query->where('school_class_id', $options['school_class_id']);
                        }

                        if (! empty($options['exam_session_id'])) {
                            $sessionId = (int) $options['exam_session_id'];

                            $query->where(function ($query) use ($sessionId) {
                                $query
                                    ->whereHas('examParticipations', fn ($query) => $query->where('exam_session_id', $sessionId))
                                    ->orWhereHas('knowledgeAnswers', fn ($query) => $query->where('exam_session_id', $sessionId))
                                    ->orWhereHas('attitudeAnswers', fn ($query) => $query->where('exam_session_id', $sessionId));
                            });
                        }

                        $jenis = $options['test_type'] ?? 'all';
                        if ($jenis !== 'all') {
                            $query->whereHas('attempts', function ($q) use ($jenis) {
                                $q->where('tipe', $jenis);
                            });
                        }
                        return $query;
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'user.sekolah',
                'user.schoolClass',
                'examSession',
            ]));
    }
}
