<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class AutenticacionTotpTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_totp_and_new_logins_challenge_the_user(): void
    {
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        $credentials = ['email' => 'estudiante@example.test', 'password' => bin2hex(random_bytes(12))];
        $engine = new class extends Google2FA
        {
            public int $step = 60_000_000;

            public function getTimestamp(): int
            {
                return $this->step;
            }
        };
        $this->app->instance(Google2FA::class, $engine);

        $this->get(route('register'))->assertOk();
        $this->post(route('register.store'), $credentials + [
            'name' => 'Estudiante',
            'password_confirmation' => $credentials['password'],
        ])->assertRedirect(route('dashboard'));

        $user = User::firstOrFail();
        $secret = decrypt($user->two_factor_secret);

        $this->get(route('dashboard'))->assertRedirect(route('totp.setup'));
        $this->get(route('totp.setup'))->assertOk()
            ->assertSee('<svg', false)->assertSee($secret)
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->delete(route('two-factor.disable'))->assertNotFound();

        $this->from(route('totp.setup'))->post(route('totp.confirm'), ['code' => 'abc'])
            ->assertSessionHasErrors('code');
        $this->assertNull($user->fresh()->two_factor_confirmed_at);

        $previousCode = $engine->getCurrentOtp($secret);
        $this->post(route('totp.confirm'), ['code' => $previousCode])
            ->assertOk()->assertSee('Guarda tus códigos de recuperación');
        $recoveryCode = $user->fresh()->recoveryCodes()[0];
        Auth::forgetGuards();

        $this->get(route('dashboard'))->assertOk()->assertSee('Estudiante')->assertSee(gethostname());
        $this->get(route('totp.setup'))->assertRedirect(route('dashboard'));
        $this->post(route('logout'))->assertRedirect();
        Auth::forgetGuards();
        $this->assertGuest();

        $this->post(route('login.store'), $credentials)->assertRedirect(route('two-factor.login'));
        $this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('two-factor.login'))->assertOk();
        $engine->step++;
        $this->post(route('two-factor.login.store'), ['code' => $previousCode])
            ->assertSessionHasErrors('code');
        $this->post(route('two-factor.login.store'), ['code' => $engine->getCurrentOtp($secret)])
            ->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertOk();

        $this->post(route('logout'))->assertRedirect();
        Auth::forgetGuards();
        $this->post(route('login.store'), $credentials)->assertRedirect(route('two-factor.login'));
        $this->post(route('two-factor.login.store'), ['recovery_code' => $recoveryCode])
            ->assertRedirect(route('dashboard'));
        $this->assertNotContains($recoveryCode, $user->fresh()->recoveryCodes());
    }

    public function test_a_password_only_session_cannot_access_the_dashboard_after_enrollment_elsewhere(): void
    {
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        $user = User::factory()->create();
        $user->forceFill([
            'two_factor_secret' => encrypt((new Google2FA)->generateSecretKey()),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_forwarded_headers_are_only_applied_for_the_balancer(): void
    {
        Route::get('/proxy-test', fn (Request $request) => [
            'scheme' => $request->getScheme(),
            'ip' => $request->ip(),
            'host' => $request->getHost(),
            'port' => $request->getPort(),
        ]);

        $headers = [
            'Host' => 'web-interno.test',
            'X-Forwarded-For' => '203.0.113.12',
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'practica.example.test',
            'X-Forwarded-Port' => '443',
        ];

        $this->withServerVariables(['REMOTE_ADDR' => '10.124.0.3'])
            ->get('http://web-interno.test/proxy-test', $headers)
            ->assertExactJson([
                'scheme' => 'https',
                'ip' => '203.0.113.12',
                'host' => 'practica.example.test',
                'port' => 443,
            ]);

        $this->withServerVariables(['REMOTE_ADDR' => '10.124.0.4'])
            ->get('http://web-interno.test/proxy-test', $headers)
            ->assertExactJson([
                'scheme' => 'http',
                'ip' => '10.124.0.4',
                'host' => 'web-interno.test',
                'port' => 80,
            ]);
    }
}
