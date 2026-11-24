<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        abort_if($request->routeIs('two-factor.disable'), 403);

        if ($request->routeIs('two-factor.enable', 'two-factor.confirm')) {
            abort_if($user->two_factor_confirmed_at !== null || $request->boolean('force'), 403);

            if ($request->routeIs('two-factor.confirm')) {
                $request->validate(['code' => ['required', 'digits:6']]);
            }

            return $next($request);
        }

        if ($user->two_factor_confirmed_at === null && ! $request->routeIs(
            'logout',
            'password.confirm',
            'password.confirm.store',
        )) {
            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }
}
