<?php

namespace App\Filament\Resources\ParticipantValidations\Pages;

use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ParticipantValidations\ParticipantValidationResource;

class EditParticipantValidation extends EditRecord
{
    protected static string $resource = ParticipantValidationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['validated_by'] = Auth::id();
        return $data;
    }
}
