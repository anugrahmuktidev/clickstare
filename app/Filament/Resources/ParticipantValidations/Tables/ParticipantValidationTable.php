<?php

namespace App\Filament\Resources\ParticipantValidations\Tables;


use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Collection;

class ParticipantValidationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.sekolah.nama')
                    ->label('Asal Sekolah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.schoolClass.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.role')
                    ->label('Role')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'valid' => 'success',
                        'invalid' => 'danger',
                    })->label('Status'),

                TextColumn::make('validator.name')
                    ->label('Divalidasi Oleh')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui'),
            ])
            ->filters([])
            ->recordActions([
                Action::make('approve')
                    ->label('ACC')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'valid',
                            'validated_by' => Auth::id(),
                        ]);

                        $record->user()->update([
                            'is_validated' => true,
                            'validated_at' => now(),
                        ]);
                    })
                    ->visible(fn($record) => $record->status === 'pending'),

                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'invalid',
                            'validated_by' => Auth::id(),
                        ]);

                        $record->user()->update([
                            'is_validated' => false,
                            'validated_at' => null,
                        ]);
                    })

                    ->visible(fn($record) => $record->status === 'pending'),

                EditAction::make()
                    ->label('Validasi')->mutateDataUsing(function (array $data): array {
                        if ($data['status'] !== 'pending') {
                            $data['validated_by'] = Auth::id();
                        }
                        return $data;
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'status' => 'valid',
                                'validated_by' => Auth::id(),
                            ]);
                            $record->user()->update([
                                'is_validated' => true,
                                'validated_at' => now(),
                            ]);
                        }
                    }),

                BulkAction::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'status' => 'invalid',
                                'validated_by' => Auth::id(),
                            ]);
                            $record->user()->update([
                                'is_validated' => false,
                                'validated_at' => null,
                            ]);
                        }
                    }),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->with(['user.sekolah', 'user.schoolClass', 'validator'])
                    ->orderByRaw("FIELD(status, 'pending', 'invalid', 'valid')")
                    ->latest('updated_at');
            })
            ->emptyStateHeading('Belum ada peserta untuk ditampilkan')
            ->emptyStateDescription('Pilih sekolah dan kelas terlebih dahulu. Jika sudah dipilih, berarti kelas tersebut belum memiliki peserta validasi.');
    }
}
