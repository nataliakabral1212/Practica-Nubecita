<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk()
        ->assertSee('Escenario')
        ->assertSee('<dd class="concert-host">'.e(gethostname()).'</dd>', escape: false)
        ->assertSee('Atendido por: '.gethostname());
});
