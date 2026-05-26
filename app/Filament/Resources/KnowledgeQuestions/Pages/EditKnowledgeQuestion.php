<?php

namespace App\Filament\Resources\KnowledgeQuestions\Pages;

use App\Filament\Resources\KnowledgeQuestions\KnowledgeQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgeQuestion extends EditRecord
{
    protected static string $resource = KnowledgeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
