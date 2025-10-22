<?php

namespace App\Filament\Resources\QuestionThreads\Pages;

use App\Filament\Resources\QuestionThreads\QuestionThreadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuestionThread extends EditRecord
{
    protected static string $resource = QuestionThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
