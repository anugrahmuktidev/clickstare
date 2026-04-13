<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends EditProfile
{
    public static function getLabel(): string
    {
        return 'Ubah Password';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('password')
                ->label('Password Baru')
                ->validationAttribute('password baru')
                ->password()
                ->revealable(filament()->arePasswordsRevealable())
                ->required()
                ->rule(Password::default())
                ->showAllValidationMessages()
                ->autocomplete('new-password')
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->same('passwordConfirmation'),
            TextInput::make('passwordConfirmation')
                ->label('Konfirmasi Password Baru')
                ->validationAttribute('konfirmasi password baru')
                ->password()
                ->revealable(filament()->arePasswordsRevealable())
                ->required()
                ->autocomplete('new-password')
                ->dehydrated(false),
        ]);
    }
}
