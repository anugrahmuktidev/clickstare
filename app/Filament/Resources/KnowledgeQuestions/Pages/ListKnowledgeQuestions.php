<?php

namespace App\Filament\Resources\KnowledgeQuestions\Pages;

use App\Filament\Resources\KnowledgeQuestions\KnowledgeQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKnowledgeQuestions extends ListRecords
{
    protected static string $resource = KnowledgeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
