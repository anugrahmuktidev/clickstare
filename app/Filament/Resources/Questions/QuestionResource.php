<?php

namespace App\Filament\Resources\Questions;

use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\Schemas\QuestionForm;
use App\Filament\Resources\Questions\Tables\QuestionsTable;
use App\Models\Question;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeft;
    protected static ?string $navigationLabel = 'Soal Pre/Post';
    protected static ?string $modelLabel      = 'Soal Pre/Post';
    protected static ?string $pluralModelLabel = 'Soal Pre/Post';
    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'teks';

    public static function form(Schema $schema): Schema
    {
        return QuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestions::route('/'),
            // 'create' => CreateQuestion::route('/create'),
            // 'edit' => EditQuestion::route('/{record}/edit'),
        ];
    }
}
