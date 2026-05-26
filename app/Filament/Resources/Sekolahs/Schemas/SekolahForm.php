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
                    ->helperText('Jika nonaktif, tombol pretest disembunyikan dan URL pretest/sikap/pengetahuan akan diblokir untuk sekolah ini.')
                    ->default(false)
                    ->inline(false),
                Toggle::make('is_posttest_enabled')
                    ->label('Tampilkan & aktifkan Posttest')
                    ->helperText('Jika nonaktif, tombol posttest disembunyikan dan URL posttest/sikap akhir/pengetahuan akhir akan diblokir untuk sekolah ini.')
                    ->default(false)
                    ->inline(false),
            ]);
    }
}
