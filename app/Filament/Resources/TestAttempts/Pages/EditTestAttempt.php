<?php

namespace App\Filament\Resources\TestAttempts\Pages;

use App\Filament\Resources\TestAttempts\TestAttemptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTestAttempt extends EditRecord
{
    protected static string $resource = TestAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $totalSoal = (int) ($this->record->total_soal ?? 0);
        $totalBenar = (int) ($data['total_benar'] ?? 0);

        $data['score'] = $totalSoal > 0 ? (int) round(($totalBenar / $totalSoal) * 100) : 0;

        return $data;
    }
}
