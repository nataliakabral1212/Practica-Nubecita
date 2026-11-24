<?php

namespace App\Http\Responses;

use Laravel\Fortify\Http\Responses\LoginResponse as FortifyLoginResponse;

class LoginResponse extends FortifyLoginResponse
{
    public function toResponse($request)
    {
        if (! $request->wantsJson() && $request->user()->two_factor_confirmed_at === null) {
            return redirect()->route('two-factor.setup');
        }

        return parent::toResponse($request);
    }
}
