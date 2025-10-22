<?php

namespace App\Filament\Resources\TestAttempts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TestAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema;
        // ->components([
        //     TextInput::make('user_id')
        //         ->required()
        //         ->numeric(),
        //     Select::make('tipe')
        //         ->options(['pre' => 'Pre', 'post' => 'Post'])
        //         ->required(),
        //     TextInput::make('skor')
        //         ->required()
        //         ->numeric()
        //         ->default(0),
        // ]);
    }
}
