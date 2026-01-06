<?php

use App\Models\User;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('sets locale from authenticated user preference', function () {
    $role = Role::firstOrCreate(['name' => 'Super Admin']);
    $permission = Permission::firstOrCreate(['name' => 'access dashboard']);
    $role->syncPermissions([$permission]);
    
    $user = User::factory()->create([
        'locale' => 'da',
    ]);
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertStatus(200);

    expect(App::getLocale())->toBe('da');
});

it('sets locale from cookie for guests', function () {
    $this->withCookie('locale', 'de')
        ->get(route('login'))
        ->assertStatus(200);

    expect(App::getLocale())->toBe('de');
});

it('uses default locale when no preference is set for guests', function () {
    $this->get(route('login'))
        ->assertStatus(200);

    expect(App::getLocale())->toBe(config('app.locale'));
});

it('uses default locale when authenticated user has no locale set', function () {
    $role = Role::firstOrCreate(['name' => 'Super Admin']);
    $permission = Permission::firstOrCreate(['name' => 'access dashboard']);
    $role->syncPermissions([$permission]);
    
    $user = User::factory()->create();
    $user->update(['locale' => null]);
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertStatus(200);

    expect(App::getLocale())->toBe(config('app.locale'));
});

it('authenticated user locale takes precedence over cookie', function () {
    $role = Role::firstOrCreate(['name' => 'Super Admin']);
    $permission = Permission::firstOrCreate(['name' => 'access dashboard']);
    $role->syncPermissions([$permission]);
    
    $user = User::factory()->create([
        'locale' => 'fr',
    ]);
    $user->assignRole($role);

    $this->withCookie('locale', 'es')
        ->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertStatus(200);

    expect(App::getLocale())->toBe('fr');
});


