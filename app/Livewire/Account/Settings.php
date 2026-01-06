<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Settings extends Component
{
    use LivewireAlert;

    public string $name;

    public string $email;

    public ?string $locale = null;

    public function mount(): void
    {
        $this->name   = auth()->user()->name;
        $this->email  = auth()->user()->email;
        $this->locale = auth()->user()->locale ?? 'en';
    }

    public function updateSettings(): void
    {

        $supportedLocales = array_keys(config('locales.supported', []));

        $this->validate([
            'name'   => 'required',
            'email'  => 'required|email|unique:users,email,'.auth()->user()->id.',id',
            'locale' => 'required|string|in:'.implode(',', $supportedLocales),
        ]);

        $previousEmail = auth()->user()->email;
        $newEmail      = $this->email;

        auth()->user()->update([
            'name'   => $this->name,
            'email'  => $this->email,
            'locale' => $this->locale,
        ]);

        if ($previousEmail !== $newEmail) {
            // logout user
            auth()->logout();
            $this->redirect(route('login'));
        } else {
            $this->alert('success', 'Settings updated successfully!');
        }
    }

    #[Layout('layouts.frontend')]
    public function render(): View
    {
        return view('livewire.account.settings');
    }
}
