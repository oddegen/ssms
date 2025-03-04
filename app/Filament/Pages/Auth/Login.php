<?php

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        $email = match (Filament::getId()) {
            'admin' => 'admin@demo.com',
            'teacher' => 'teacher@demo.com',
            'student' => 'student@demo.com',
            default => '',
        };

        $this->form->fill([
            'email' => $email,
            'password' => 'password',
            'remember' => true,
        ]);
    }
}
