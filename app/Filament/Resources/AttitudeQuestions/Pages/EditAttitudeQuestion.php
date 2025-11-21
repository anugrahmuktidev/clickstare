<?php

namespace App\Filament\Resources\AttitudeQuestions\Pages;

use App\Filament\Resources\AttitudeQuestions\AttitudeQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttitudeQuestion extends EditRecord
{
    protected static string $resource = AttitudeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
