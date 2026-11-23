<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ProtectFortifyRoutes
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if($request->routeIs(
            'password.confirm',
            'password.confirm.store',
            'password.confirmation',
            'two-factor.enable',
            'two-factor.disable',
            'two-factor.confirm',
            'two-factor.qr-code',
            'two-factor.secret-key',
            'two-factor.recovery-codes',
            'two-factor.regenerate-recovery-codes',
        ), 404);

        $userId = $request->session()->get('login.id');

        if ($request->routeIs('two-factor.login.store') && $userId !== null) {
            // Serialize verification so two requests cannot consume the same recovery code.
            try {
                return Cache::lock('totp-challenge:'.$userId, 15)
                    ->block(3, fn () => $next($request));
            } catch (LockTimeoutException) {
                abort(429, __('Too Many Attempts.'));
            }
        }

        return $next($request);
    }
}
