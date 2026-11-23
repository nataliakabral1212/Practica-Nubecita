<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireConfirmedTotp
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('totp.setup');
        }

        if ($request->session()->get('totp.user_id') !== $request->user()->getAuthIdentifier()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
