<?php

namespace App\Filament\Resources\QuestionThreads\Pages;

use App\Filament\Resources\QuestionThreads\QuestionThreadResource;
use Filament\Resources\Pages\ViewRecord;

class ViewThread extends ViewRecord
{
    protected static string $resource = QuestionThreadResource::class;
    // Relation Manager "Balasan" akan tampil di bawah
}
