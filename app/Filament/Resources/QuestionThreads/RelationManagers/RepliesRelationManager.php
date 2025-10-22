<?php
// app/Filament/Resources/QuestionThreads/RelationManagers/RepliesRelationManager.php

namespace App\Filament\Resources\QuestionThreads\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;

use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';
    protected static ?string $title = 'Balasan';

    // 🔓 pastikan selalu tampil & boleh create
    // public static function canViewForRecord($owner, string $pageClass): bool
    // {
    //     return true;
    // }

    public static function canViewForRecord($owner, string $pageClass): bool
    {
        return true;
    }

    public  function canCreate(): bool
    {
        return true;
    }
    public  function canEdit($record): bool
    {
        return true;
    }
    public  function canDelete($record): bool
    {
        return true;
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Textarea::make('isi')->label('Balasan')->rows(4)->required(),
            Forms\Components\Toggle::make('is_solution')->label('Tandai sebagai solusi'),
        ]);
    }

    private function canModerateThread(): bool
    {
        $u = Auth::user();
        $t = $this->getOwnerRecord(); // QuestionThread
        if (! $u || ! $t) return false;

        return $u->role === 'admin'
            || ($u->role === 'guru' && (int) $u->sekolah_id === (int) $t->sekolah_id);
    }


    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Oleh')->sortable()->default('—'),
                Tables\Columns\IconColumn::make('is_solution')->label('Solusi')->boolean(),
                Tables\Columns\TextColumn::make('isi')
                    ->label('Isi')
                    ->wrap()
                    ->toggleable(false),
                Tables\Columns\TextColumn::make('created_at')->since()->label('Waktu')->sortable(),
            ])
            ->headerActions([
                CreateAction::make('reply')
                    ->label('Balas')
                    ->visible(true)            // tampil apa pun
                    ->authorize(fn() => true) // abaikan policy/gate
                    ->mutateDataUsing(fn(array $data) => $data + ['user_id' => Auth::id()])
                    ->after(function ($record) {
                        $thread = $record->thread;
                        if ($record->is_solution) {
                            $thread->update(['status' => 'closed', 'solved_at' => now()]);
                            $thread->replies()->where('id', '!=', $record->id)->update(['is_solution' => false]);
                        }
                    }),
            ])
            // ->recordActions([
            //     EditAction::make()->label('Ubah')
            //         ->authorize(fn() => true)
            //         ->mutateFormDataUsing(fn(array $data) => $data + ['user_id' => Auth::id()]),
            //     DeleteAction::make()->label('Hapus')->authorize(fn() => true),
            // ])
            ->recordActions([
                EditAction::make()->label('Ubah')
                    ->authorize(fn($record) => $this->canModerateThread())
                    ->mutateDataUsing(fn(array $data) => $data + ['user_id' => Auth::id()])
                    ->after(function ($record) {
                        $thread = $record->thread;
                        if ($record->is_solution) {
                            $thread->update(['status' => 'closed', 'solved_at' => now()]);
                            $thread->replies()->where('id', '!=', $record->id)->update(['is_solution' => false]);
                        } else {
                            if (! $thread->replies()->where('is_solution', true)->exists()) {
                                $thread->update(['status' => 'open', 'solved_at' => null]);
                            }
                        }
                    }),

                DeleteAction::make()->label('Hapus')
                    ->authorize(fn($record) => $this->canModerateThread()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()->label('Hapus yang dipilih')]),
            ]);
    }
}
