<?php
// app/Filament/Resources/QuestionThreads/QuestionThreadResource.php

namespace App\Filament\Resources\QuestionThreads;

use BackedEnum;
use App\Models\QuestionThread;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use App\Filament\Resources\QuestionThreads\Pages\ViewThread;
use App\Filament\Resources\QuestionThreads\Pages\ListThreads;
use App\Filament\Resources\QuestionThreads\Tables\QuestionThreadsTable;
use App\Filament\Resources\QuestionThreads\RelationManagers\RepliesRelationManager;

class QuestionThreadResource extends Resource
{
    protected static ?string $model = QuestionThread::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Tanya Jawab Siswa';
    protected static ?string $modelLabel      = 'Tanya Jawab Siswa';
    protected static ?string $pluralModelLabel = 'Tanya Jawab Siswa';
    protected static ?int $navigationSort = 40;

    // 🔓 izinkan semua (sementara)
    public static function canViewAny(): bool
    {
        return true;
    }
    public static function canView($record): bool
    {
        return true;
    }
    public static function canCreate(): bool
    {
        return true;
    }
    public static function canEdit($record): bool
    {
        return true;
    }
    public static function canDelete($record): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThreads::route('/'),
            'view'  => ViewThread::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [RepliesRelationManager::class];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Pertanyaan')
                ->schema([
                    Text::make(fn (QuestionThread $record) => 'Pertanyaan: ' . ($record->isi ?: '—'))
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        Text::make(fn (QuestionThread $record) => 'Penanya: ' . (optional($record->asker)->name ?? '—')),

                        Text::make(fn (QuestionThread $record) => 'Sekolah: ' . (optional($record->sekolah)->nama ?? '—')),

                        Text::make(fn (QuestionThread $record) => $record->status ? 'Status: ' . ucfirst($record->status) : 'Status: —')
                            ->badge(fn (QuestionThread $record) => filled($record->status))
                            ->color(fn (QuestionThread $record) => match ($record->status) {
                                'closed' => 'success',
                                'open' => 'warning',
                                default => 'gray',
                            }),

                        Text::make(fn (QuestionThread $record) => 'Dibuat: ' . (optional($record->created_at)->diffForHumans() ?: '—')),
                    ])->columnSpanFull(),
                ])->columns(1),
        ]);
    }

    // 🔓 tampilkan semua thread (tanpa filter role)
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->with(['asker:id,name', 'sekolah:id,nama'])
    //         ->withCount(['replies as has_solution' => fn($q) => $q->where('is_solution', true)]);
    // }
    public static function getEloquentQuery(): Builder
    {
        $u = Auth::user();

        $q = parent::getEloquentQuery()
            ->with(['asker:id,name', 'sekolah:id,nama'])
            ->withCount(['replies as has_solution' => fn($x) => $x->where('is_solution', true)]);

        if ($u?->role === 'admin') return $q;
        if ($u?->role === 'guru')  return $q->where('sekolah_id', $u->sekolah_id);

        // siswa tidak boleh masuk panel
        return $q->whereRaw('1=0');
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return QuestionThreadsTable::configure($table);
    }
}
