<?php

namespace App\Filament\Resources\AttitudeQuestions\Pages;

use App\Filament\Resources\AttitudeQuestions\AttitudeQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttitudeQuestions extends ListRecords
{
    protected static string $resource = AttitudeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
