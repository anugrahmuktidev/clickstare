<?php

namespace App\Filament\Resources\ParticipantValidations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ParticipantValidationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('validated_by')
                    ->disabled()
                    ->dehydrated(false)
                    ->default(fn() => Auth::user()?->name),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'valid'   => 'Valid',
                        'invalid' => 'Invalid',
                    ])
                    ->required(),
                Textarea::make('catatan')
                    ->columnSpanFull(),
            ])->mutateFormDataUsing(function (array $data): array {
                if ($data['status'] !== 'pending') {
                    $data['validated_by'] = Auth::id(); // simpan id admin
                }
                return $data;
            });
    }
}
