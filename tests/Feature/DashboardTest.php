<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('users with an unconfirmed two factor secret cannot visit protected routes', function () {
    $user = User::factory()->withTwoFactor()->create(['two_factor_confirmed_at' => null]);

    $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('two-factor.setup'));
    $this->get(route('profile.edit'))->assertRedirect(route('two-factor.setup'));
    $this->get(route('two-factor.show'))->assertRedirect(route('two-factor.setup'));
    $this->get(route('verification.notice'))->assertRedirect(route('two-factor.setup'));
});

test('users with confirmed two factor authentication can visit the dashboard', function () {
    $user = User::factory()->withTwoFactor()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk()
        ->assertSee('Escenario')
        ->assertSee('<dd class="concert-host">'.e(gethostname()).'</dd>', escape: false)
        ->assertSee('Atendido por: '.gethostname());
});
