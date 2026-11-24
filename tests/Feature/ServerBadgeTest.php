<?php

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

test('the server badge is visible on the public pages', function (string $route) {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('Atendido por: '.gethostname());
})->with(['home', 'login', 'register', 'password.request']);

test('a registered user can enroll with an SVG QR and sign in with a TOTP', function () {
    $totp = new class extends Google2FA
    {
        public int $timestamp = 100000;

        public function getTimestamp(): int
        {
            return $this->timestamp;
        }
    };
    $this->app->instance(Google2FA::class, $totp);

    $credentials = ['email' => 'totp@example.com', 'password' => 'test-password'];
    $badge = 'Atendido por: '.gethostname();

    $this->post(route('register.store'), [
        ...$credentials,
        'name' => 'TOTP Test',
        'password_confirmation' => $credentials['password'],
    ])->assertSessionHasNoErrors()->assertRedirect(route('two-factor.setup'));

    $user = User::where('email', $credentials['email'])->firstOrFail();
    $this->get(route('two-factor.setup'))->assertOk()->assertSee($badge)
        ->assertSee($user->twoFactorQrCodeSvg(), escape: false);
    $secret = decrypt($user->two_factor_secret);
    $setupCode = $totp->getCurrentOtp($secret);

    $this->from(route('two-factor.setup'))->post(route('two-factor.confirm'), ['code' => 'abc'])
        ->assertSessionHasErrors('code');
    $this->get(route('dashboard'))->assertRedirect(route('two-factor.setup'));
    $this->from(route('two-factor.setup'))->post(route('two-factor.confirm'), ['code' => $setupCode])
        ->assertSessionHasNoErrors()->assertRedirect(route('two-factor.setup'));

    $user->refresh();
    expect($user->two_factor_confirmed_at)->not->toBeNull();
    expect($user->recoveryCodes())->toHaveCount(8);
    $this->get(route('two-factor.setup'))->assertOk()->assertSee($user->recoveryCodes()[0]);
    $this->get(route('dashboard'))->assertOk()->assertSee($badge);

    $this->post(route('logout'))->assertRedirect('/');
    $this->assertGuest();

    $this->post(route('login.store'), $credentials)->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
    $this->get(route('two-factor.login'))->assertOk()->assertSee($badge);

    $totp->timestamp++;
    if ($totp->getCurrentOtp($secret) === $setupCode) {
        $totp->timestamp++;
    }

    $this->post(route('two-factor.login.store'), ['code' => $totp->getCurrentOtp($secret)])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($user);
});
