<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;

it('can render the account settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('account.settings'))
        ->assertStatus(200);
});

it('can update account settings', function () {
    $user = User::factory()->create([
        'name'   => 'John Doe',
        'email'  => 'john@example.com',
        'locale' => 'en',
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Account\Settings::class)
        ->set('name', 'Jane Doe')
        ->set('locale', 'da')
        ->call('updateSettings')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id'     => $user->id,
        'name'   => 'Jane Doe',
        'email'  => 'john@example.com',
        'locale' => 'da',
    ]);
});

it('can update locale preference from frontend', function () {
    $user = User::factory()->create(['locale' => 'en']);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Account\Settings::class)
        ->set('locale', 'fr')
        ->call('updateSettings')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id'     => $user->id,
        'locale' => 'fr',
    ]);
});

it('validates locale is a valid option in frontend', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\Account\Settings::class)
        ->set('locale', 'invalid')
        ->call('updateSettings')
        ->assertHasErrors(['locale']);
});

it('requires a locale to be set in frontend', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\Account\Settings::class)
        ->set('locale', '')
        ->call('updateSettings')
        ->assertHasErrors(['locale']);
});

it('can logout user if email is changed in frontend', function () {
    $user = User::factory()->create([
        'email' => 'old@example.com',
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Account\Settings::class)
        ->set('email', 'new@example.com')
        ->call('updateSettings')
        ->assertRedirect(route('login'));

    $this->assertGuest();

    $this->assertDatabaseHas('users', [
        'id'    => $user->id,
        'email' => 'new@example.com',
    ]);
});
