<?php

namespace App\Filament\Resources\ParticipantValidations;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Models\ParticipantValidation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\ParticipantValidations\Tables\ParticipantValidationTable;



class ParticipantValidationResource extends Resource
{
    protected static ?string $model = ParticipantValidation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;
    protected static ?string $navigationLabel = 'Validasi Peserta';
    protected static ?string $modelLabel      = 'Validasi Peserta';
    protected static ?string $pluralModelLabel = 'Validasi Peserta';
    protected static ?int $navigationSort = 10;
    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Peserta')
                    ->disabled(), // tidak bisa ubah peserta

                Select::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'valid' => 'Disetujui',
                        'invalid' => 'Ditolak',
                    ])
                    ->required(),

                // Textarea::make('catatan')
                //     ->label('Catatan')
                //     ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return ParticipantValidationTable::configure($table);
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParticipantValidations::route('/'),
            // 'edit'  => Pages\EditParticipantValidation::route('/{record}/edit'),
        ];
    }
}
