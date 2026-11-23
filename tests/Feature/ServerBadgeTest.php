<?php

use App\Models\User;
use Livewire\Livewire;
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
    ])->assertSessionHasNoErrors()->assertRedirect(route('dashboard', absolute: false));

    $this->get(route('dashboard'))->assertOk()->assertSee($badge);
    $this->post(route('password.confirm.store'), ['password' => $credentials['password']])
        ->assertSessionHasNoErrors();
    $this->get(route('two-factor.show'))->assertOk()->assertSee($badge);

    $setup = Livewire::test('pages::settings.two-factor-setup-modal', ['requiresConfirmation' => true])
        ->dispatch('start-two-factor-setup')
        ->assertHasNoErrors();

    expect($setup->get('qrCodeSvg'))->toStartWith('<svg');
    $secret = $setup->get('manualSetupKey');
    $setupCode = $totp->getCurrentOtp($secret);

    $setup->call('showVerificationIfNecessary')
        ->set('code', $setupCode)
        ->call('confirmTwoFactor')
        ->assertHasNoErrors()
        ->assertDispatched('two-factor-enabled');

    $user = User::where('email', $credentials['email'])->firstOrFail();
    expect($user->two_factor_confirmed_at)->not->toBeNull();
    expect($user->recoveryCodes())->toHaveCount(8);
    $recoveryPage = $this->get(route('two-factor.show'))->assertOk();
    expect(str_contains($recoveryPage->getContent(), $user->recoveryCodes()[0]))->toBeTrue();

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
