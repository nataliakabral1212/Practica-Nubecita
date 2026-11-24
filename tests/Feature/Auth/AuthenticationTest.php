<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('existing users without two factor authentication must set it up after login', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('two-factor.setup'));

    $this->assertAuthenticated();
    $this->get(route('two-factor.setup'))->assertOk()->assertSee('Confirm password');
    $this->get(route('dashboard'))->assertRedirect(route('two-factor.setup'));

    $this->from(route('two-factor.setup'))->post(route('two-factor.enable'))
        ->assertRedirect(route('password.confirm'));
    $this->get(route('two-factor.setup'));
    $this->post(route('password.confirm.store'), ['password' => 'password'])
        ->assertRedirect(route('two-factor.setup'));
    $this->from(route('two-factor.setup'))->post(route('two-factor.enable'))
        ->assertRedirect(route('two-factor.setup'));
    $this->get(route('two-factor.setup'))->assertOk()->assertSee('<svg', escape: false);
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users without confirmed two factor authentication can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});
