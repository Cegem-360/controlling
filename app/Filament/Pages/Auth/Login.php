<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BasePage;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;

final class Login extends BasePage
{
    public string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament.layouts.auth';

    public function mount(): void
    {
        parent::mount();

        if (app()->environment('local')) {
            $this->form->fill([
                'email' => 'admin@admin.com',
                'password' => 'password',
                'remember' => true,
            ]);
        }
    }

    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label(__('Enter your work email'))
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->placeholder('example@company.com')
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): TextInput
    {
        return TextInput::make('password')
            ->label(__('Password'))
            ->password()
            ->revealable()
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Checkbox
    {
        return Checkbox::make('remember')
            ->label(__('Remember me'))
            ->extraInputAttributes(['tabindex' => 3]);
    }
}
