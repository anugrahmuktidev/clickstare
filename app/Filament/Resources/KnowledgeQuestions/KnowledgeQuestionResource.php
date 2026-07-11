<?php

namespace App\Filament\Resources\KnowledgeQuestions;

use App\Filament\Resources\KnowledgeQuestions\Pages\CreateKnowledgeQuestion;
use App\Filament\Resources\KnowledgeQuestions\Pages\EditKnowledgeQuestion;
use App\Filament\Resources\KnowledgeQuestions\Pages\ListKnowledgeQuestions;
use App\Filament\Resources\KnowledgeQuestions\Schemas\KnowledgeQuestionForm;
use App\Filament\Resources\KnowledgeQuestions\Tables\KnowledgeQuestionsTable;
use App\Models\KnowledgeQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KnowledgeQuestionResource extends Resource
{
    protected static ?string $model = KnowledgeQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Soal Pengetahuan';
    protected static ?string $modelLabel = 'Soal Pengetahuan';
    protected static ?string $pluralModelLabel = 'Soal Pengetahuan';
    protected static ?int $navigationSort = 46;
    protected static ?string $recordTitleAttribute = 'teks';

    public static function form(Schema $schema): Schema
    {
        return KnowledgeQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeQuestionsTable::configure($table);
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
            'index' => ListKnowledgeQuestions::route('/'),
            'create' => CreateKnowledgeQuestion::route('/create'),
            'edit' => EditKnowledgeQuestion::route('/{record}/edit'),
        ];
    }
}
