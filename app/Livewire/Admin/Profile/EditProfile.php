<?php

namespace App\Livewire\Admin\Profile;

use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditProfile extends Component
{
    use LivewireAlert;

    public $name;
    public $email;
    public $locale;

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
        $this->locale = auth()->user()->locale ?? 'en';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id . ',id',
            'locale' => 'required|string|in:en,da,de,es,fr,it,nl,pt,sv',
        ]);

        $previousEmail = auth()->user()->email;
        $newEmail = $this->email;

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
            'locale' => $this->locale,
        ]);

        if ($previousEmail !== $newEmail) {
            // logout user
            auth()->logout();
            $this->redirect(route('login'));
        } else {
            $this->alert('success', 'Profile updated successfully!');
        }
    }

    public function render(): View
    {
        return view('livewire.admin.profile.edit-profile')->extends('layouts.admin');
    }
}
