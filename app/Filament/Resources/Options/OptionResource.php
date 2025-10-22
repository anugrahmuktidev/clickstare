<?php

namespace App\Filament\Resources\Options;

use App\Filament\Resources\Options\Pages\CreateOption;
use App\Filament\Resources\Options\Pages\EditOption;
use App\Filament\Resources\Options\Pages\ListOptions;
use App\Filament\Resources\Options\Schemas\OptionForm;
use App\Filament\Resources\Options\Tables\OptionsTable;
use App\Filament\Resources\Questions\RelationManagers\OptionsRelationManager;
use App\Models\Option;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OptionResource extends Resource
{
    protected static ?string $model = Option::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
    protected static ?string $navigationLabel = 'Jawaban';
    protected static ?string $modelLabel      = 'Jawaban';
    protected static ?string $pluralModelLabel = 'Jawaban';
    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'Jawaban';

    public static function form(Schema $schema): Schema
    {
        return OptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OptionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOptions::route('/'),
            // 'create' => CreateOption::route('/create'),
            // 'edit' => EditOption::route('/{record}/edit'),
        ];
    }
}
