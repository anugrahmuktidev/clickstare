<?php

namespace App\Filament\Resources\ParticipantValidations\Pages;

use App\Filament\Resources\ParticipantValidations\ParticipantValidationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParticipantValidations extends ListRecords
{
    protected static string $resource = ParticipantValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make()->label('Tambah Validasi'),
        ];
    }
}
