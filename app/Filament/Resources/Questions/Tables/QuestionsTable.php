<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor')
                    ->label('No.')
                    ->sortable(),
                TextColumn::make('teks'),
                TextColumn::make('tipe'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe Soal')
                    ->options([
                        'pre'  => 'PRE',
                        'post' => 'POST',
                    ])
                    ->indicator('Tipe'), // chip kecil di header saat aktif
            ])
            ->recordActions([
                Action::make('copyToPosttest')
                    ->label('Salin ke Posttest')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Question $record) => $record->tipe === 'pre')
                    ->action(function (Question $record) {
                        $alreadyExists = Question::where('tipe', 'post')
                            ->where('teks', $record->teks)
                            ->exists();

                        if ($alreadyExists) {
                            Notification::make()
                                ->warning()
                                ->title('Sudah ada di posttest')
                                ->body('Soal dengan teks yang sama sudah tersedia pada posttest.')
                                ->send();

                            return;
                        }

                        $copy = self::duplicateQuestionToPost($record);

                        Notification::make()
                            ->success()
                            ->title('Soal disalin')
                            ->body("Soal baru (#{$copy->nomor}) berhasil ditambahkan ke posttest.")
                            ->send();
                    }),
                EditAction::make(),
                DeleteBulkAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('copyToPosttest')
                        ->label('Salin ke Posttest')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $copied = 0;

                            foreach ($records as $record) {
                                if ($record->tipe !== 'pre') {
                                    continue;
                                }

                                $alreadyExists = Question::where('tipe', 'post')
                                    ->where('teks', $record->teks)
                                    ->exists();

                                if ($alreadyExists) {
                                    continue;
                                }

                                self::duplicateQuestionToPost($record);
                                $copied++;
                            }

                            $notification = Notification::make();

                            if ($copied === 0) {
                                $notification
                                    ->warning()
                                    ->title('Tidak ada soal yang disalin')
                                    ->body('Pastikan soal pretest dipilih dan belum ada duplikat di posttest.');
                            } else {
                                $notification
                                    ->success()
                                    ->title('Soal disalin')
                                    ->body("{$copied} soal berhasil disalin ke posttest.");
                            }

                            $notification->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function duplicateQuestionToPost(Question $record): Question
    {
        $record->loadMissing('options');

        return DB::transaction(function () use ($record) {
            $copy = Question::create([
                'tipe' => 'post',
                'teks' => $record->teks,
            ]);

            $payload = $record->options
                ->map(fn($option) => [
                    'teks' => $option->teks,
                    'benar' => $option->benar,
                ])
                ->all();

            if ($payload) {
                $copy->options()->createMany($payload);
            }

            return $copy;
        });
    }
}
