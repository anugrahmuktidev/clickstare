<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

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
