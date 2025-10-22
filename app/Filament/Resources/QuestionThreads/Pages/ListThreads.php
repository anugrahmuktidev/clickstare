<?php

namespace App\Filament\Resources\QuestionThreads\Pages;

use App\Filament\Resources\QuestionThreads\QuestionThreadResource;
use Filament\Resources\Pages\ListRecords;

class ListThreads extends ListRecords
{
    protected static string $resource = QuestionThreadResource::class;

    protected function getHeaderActions(): array
    {
        return []; // thread dibuat oleh siswa di frontend
    }
}
