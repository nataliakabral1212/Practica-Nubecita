<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;

class TotpEnrollmentController
{
    public function show(Request $request, EnableTwoFactorAuthentication $enable): Response|RedirectResponse
    {
        $user = DB::transaction(function () use ($request, $enable): User {
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->getKey());

            if (! $user->two_factor_secret) {
                $enable($user);
            }

            return $user;
        });

        if ($user->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('dashboard');
        }

        return response()->view('auth.setup', ['user' => $user])
            ->header('Cache-Control', 'no-store');
    }

    public function confirm(Request $request, ConfirmTwoFactorAuthentication $confirm): Response|RedirectResponse
    {
        $input = $request->validate(['code' => ['required', 'string', 'digits:6']]);

        $codes = DB::transaction(function () use ($request, $input, $confirm): ?array {
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->getKey());

            if ($user->hasEnabledTwoFactorAuthentication() || ! $user->two_factor_secret) {
                return null;
            }

            $confirm($user, $input['code']);

            return $user->recoveryCodes();
        });

        if ($codes === null) {
            return redirect()->route('totp.setup');
        }

        $request->session()->put('totp.user_id', $request->user()->getAuthIdentifier());
        $request->session()->regenerate();

        return response()->view('auth.recovery', ['codes' => $codes])
            ->header('Cache-Control', 'no-store');
    }
}
