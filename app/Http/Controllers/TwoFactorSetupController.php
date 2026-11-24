<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorSetupController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $confirmed = $user->two_factor_confirmed_at !== null;

        if ($confirmed && $request->session()->get('status') !== Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED) {
            return redirect()->intended(route('dashboard'));
        }

        $passwordConfirmed = ! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            || Date::now()->unix() - $request->session()->get('auth.password_confirmed_at', 0) <= config('auth.password_timeout');

        if (! $passwordConfirmed) {
            $request->session()->put('url.intended', route('two-factor.setup'));
        }

        return response()->view('pages::auth.two-factor-setup', [
            'confirmed' => $confirmed,
            'passwordConfirmed' => $passwordConfirmed,
            'qrCode' => $passwordConfirmed && ! $confirmed && $user->two_factor_secret
                ? $user->twoFactorQrCodeSvg()
                : null,
            'recoveryCodes' => $passwordConfirmed && $confirmed ? $user->recoveryCodes() : [],
        ])->header('Cache-Control', 'no-store, private');
    }
}
