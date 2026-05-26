<?php

namespace App\Filament\Resources\KnowledgeQuestions\Pages;

use App\Filament\Resources\KnowledgeQuestions\KnowledgeQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledgeQuestion extends CreateRecord
{
    protected static string $resource = KnowledgeQuestionResource::class;
}
