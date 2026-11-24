<?php

use App\Models\User;
use Livewire\Livewire;

test('two factor settings show enabled status and recovery controls', function () {
    $user = User::factory()->withTwoFactor()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'))
        ->assertOk()
        ->assertSee('Enabled')
        ->assertSee('Regenerate codes')
        ->assertDontSee('Disable 2FA');
});

test('two factor settings require password confirmation', function () {
    $this->actingAs(User::factory()->withTwoFactor()->create())
        ->get(route('two-factor.show'))
        ->assertRedirect(route('password.confirm'));
});

test('two factor authentication cannot be disabled through Fortify', function () {
    $user = User::factory()->withTwoFactor()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->delete(route('two-factor.disable'))
        ->assertForbidden();

    expect($user->fresh()->hasEnabledTwoFactorAuthentication())->toBeTrue();
});

test('two factor recovery codes can still be regenerated', function () {
    $user = User::factory()->withTwoFactor()->create();
    $previousCodes = $user->recoveryCodes();

    $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);
    Livewire::test('pages::settings.two-factor.recovery-codes')
        ->call('regenerateRecoveryCodes')
        ->assertHasNoErrors();

    expect($user->fresh()->recoveryCodes())->toHaveCount(8)->not->toBe($previousCodes);
});
