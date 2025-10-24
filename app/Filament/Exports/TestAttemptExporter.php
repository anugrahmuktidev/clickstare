<?php

namespace App\Filament\Exports;

use App\Models\TestAttempt;
use App\Models\Question;
use App\Models\Sekolah;
use Filament\Forms\Components\Select;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class TestAttemptExporter extends Exporter
{
    protected static ?string $model = TestAttempt::class;

    public function getJobQueue(): ?string
    {
        return null;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getColumns(): array
    {
        // Tentukan jumlah kolom P1..Pn berdasarkan nomor soal maksimum di DB
        $maxNomor = (int) (Question::max('nomor') ?? 0);
        if ($maxNomor < 1) {
            $maxNomor = 10; // fallback aman
        }

        // Helper untuk mapping per-soal: 'B' jika benar, 'S' jika salah, kosong jika tidak ada
        $perSoal = function (int $nomor) {
            return function ($record) use ($nomor) {
                $answers = $record->relationLoaded('answers') ? $record->answers : $record->answers()->with('question:id,nomor')->get();
                foreach ($answers as $ans) {
                    $n = (int) ($ans->question->nomor ?? 0);
                    if ($n === $nomor) {
                        return $ans->is_correct ? 'B' : 'S';
                    }
                }
                return '';
            };
        };

        $columns = [
            ExportColumn::make('user.name')->label('Nama'),
            ExportColumn::make('user.sekolah.nama')->label('Sekolah'),
            ExportColumn::make('tipe')->label('Jenis Tes'),
        ];

        for ($i = 1; $i <= $maxNomor; $i++) {
            $columns[] = ExportColumn::make('P' . $i)
                ->label('P' . $i)
                ->state($perSoal($i));
        }

        $columns[] = ExportColumn::make('total')
            ->label('Total')
            ->state(function ($record) {
                if (! is_null($record->total_benar)) {
                    return (string) (int) $record->total_benar;
                }
                $answers = $record->relationLoaded('answers') ? $record->answers : $record->answers()->get();
                return (string) $answers->where('is_correct', true)->count();
            });

        return $columns;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('sekolah_id')
                ->label('Sekolah')
                ->options(fn () => Sekolah::orderBy('nama')->pluck('nama', 'id')->all())
                ->searchable()
                ->preload()
                ->placeholder('Semua'),

            Select::make('test_type')
                ->label('Jenis Tes')
                ->options([
                    'all' => 'Semua',
                    'pre' => 'Pretest',
                    'post' => 'Posttest',
                ])
                ->default('all'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query
            ->with([
                'user:id,name,sekolah_id',
                'answers' => fn ($q) => $q->with('question:id,nomor'),
            ]);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'File export hasil tes sudah siap diunduh.';
    }
}
