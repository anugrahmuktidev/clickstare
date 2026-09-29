<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SekolahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('alamat'),
                Repeater::make('classes')
                    ->label('Kelas yang akan diuji')
                    ->relationship('classes')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama kelas')
                            ->placeholder('Contoh: 7A')
                            ->required()
                            ->maxLength(50),
                    ])
                    ->addActionLabel('Tambah kelas')
                    ->defaultItems(1)
                    ->reorderable(false)
                    ->columnSpanFull(),
                Repeater::make('examSessions')
                    ->label('Sesi test')
                    ->relationship('examSessions')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama sesi')
                            ->placeholder('Contoh: Sesi 1')
                            ->required()
                            ->maxLength(100),
                        Toggle::make('is_active')
                            ->label('Sesi aktif')
                            ->helperText('Siswa hanya dapat memilih sesi yang aktif.')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->addActionLabel('Tambah sesi test')
                    ->defaultItems(1)
                    ->reorderable(false)
                    ->columnSpanFull(),
                Toggle::make('is_pretest_enabled')
                    ->label('Tampilkan & aktifkan Pretest')
                    ->helperText('Jika nonaktif, siswa tidak dapat melihat tombol Pretest atau membuka rangkaian Pretest (pengetahuan lalu sikap).')
                    ->default(false)
                    ->inline(false),
                Toggle::make('is_posttest_enabled')
                    ->label('Tampilkan & aktifkan Posttest')
                    ->helperText('Jika nonaktif, siswa tidak dapat melihat tombol Posttest atau membuka rangkaian Posttest (pengetahuan lalu sikap).')
                    ->default(false)
                    ->inline(false),
            ]);
    }
}
