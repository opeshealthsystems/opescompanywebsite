<?php

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function getView(): string
    {
        return 'filament.pages.auth.login';
    }

    public function loginAsDemo(string $email): void
    {
        if (! Filament::auth()->attempt(['email' => $email, 'password' => 'demo1234'])) {
            $this->addError('data.email', 'Demo login failed — user not found.');
            return;
        }

        session()->regenerate();

        $this->redirect(Filament::getUrl());
    }
}
