<?php
// app/Filament/Resources/QuestionThreads/Schemas/ThreadForm.php
namespace App\Filament\Resources\QuestionThreads\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class ThreadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Select::make('status')
                ->label('Status')
                ->options(['open' => 'Open', 'closed' => 'Closed'])
                ->required(),
            Textarea::make('isi')
                ->label('Isi Pertanyaan')
                ->disabled()
                ->rows(6),
        ]);
    }
}
