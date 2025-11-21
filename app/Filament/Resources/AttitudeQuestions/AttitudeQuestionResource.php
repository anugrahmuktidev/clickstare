<?php

namespace App\Filament\Resources\AttitudeQuestions;

use App\Filament\Resources\AttitudeQuestions\Pages\CreateAttitudeQuestion;
use App\Filament\Resources\AttitudeQuestions\Pages\EditAttitudeQuestion;
use App\Filament\Resources\AttitudeQuestions\Pages\ListAttitudeQuestions;
use App\Filament\Resources\AttitudeQuestions\Schemas\AttitudeQuestionForm;
use App\Filament\Resources\AttitudeQuestions\Tables\AttitudeQuestionsTable;
use App\Models\AttitudeQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttitudeQuestionResource extends Resource
{
    protected static ?string $model = AttitudeQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Pertanyaan Sikap';
    protected static ?string $modelLabel = 'Pertanyaan Sikap';
    protected static ?string $pluralModelLabel = 'Pertanyaan Sikap';
    protected static ?int $navigationSort = 45;
    protected static ?string $recordTitleAttribute = 'teks';

    public static function form(Schema $schema): Schema
    {
        return AttitudeQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttitudeQuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttitudeQuestions::route('/'),
            'create' => CreateAttitudeQuestion::route('/create'),
            'edit' => EditAttitudeQuestion::route('/{record}/edit'),
        ];
    }
}
