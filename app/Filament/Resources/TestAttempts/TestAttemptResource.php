<?php

namespace App\Filament\Resources\TestAttempts;

use App\Filament\Resources\TestAttempts\Pages\CreateTestAttempt;
use App\Filament\Resources\TestAttempts\Pages\EditTestAttempt;
use App\Filament\Resources\TestAttempts\Pages\ListTestAttempts;
use App\Filament\Resources\TestAttempts\Schemas\TestAttemptForm;
use App\Filament\Resources\TestAttempts\Tables\TestAttemptsTable;
use App\Models\TestAttempt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TestAttemptResource extends Resource
{
    protected static ?string $model = TestAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'Hasil Tes';
    protected static ?string $modelLabel      = 'Hasil Tes';
    protected static ?string $pluralModelLabel = 'Hasil Tes';
    protected static ?int $navigationSort = 70;

    protected static ?string $recordTitleAttribute = 'hasil test';

    public static function form(Schema $schema): Schema
    {
        return TestAttemptForm::configure($schema);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
        ];
    }

    public static function table(Table $table): Table
    {
        return TestAttemptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\TestAttempts\RelationManagers\AnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestAttempts::route('/'),
            'view' => Pages\ViewTestAttempt::route('/{record}'),
            // 'create' => CreateTestAttempt::route('/create'),
            // 'edit' => EditTestAttempt::route('/{record}/edit'),
        ];
    }
}
