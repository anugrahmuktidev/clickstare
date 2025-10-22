<?php

namespace App\Filament\Resources\Videos;


use BackedEnum;
use App\Models\Video;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Videos\Pages\ListVideos;
use App\Filament\Resources\Videos\Schemas\VideoForm;
use App\Filament\Resources\Videos\Tables\VideosTable;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;
    protected static ?string $navigationLabel = 'Video';
    protected static ?string $modelLabel      = 'Video';
    protected static ?string $pluralModelLabel = 'Video';
    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'Videos';

    public static function form(Schema $schema): Schema
    {
        return VideoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideosTable::configure($table);
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
            'index' => ListVideos::route('/'),
            // 'create' => CreateVideo::route('/create'),
            // 'edit' => EditVideo::route('/{record}/edit'),
        ];
    }
}
