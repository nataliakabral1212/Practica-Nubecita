<?php

namespace App\Http\Responses;

use Laravel\Fortify\Http\Responses\RegisterResponse as FortifyRegisterResponse;

class RegisterResponse extends FortifyRegisterResponse
{
    public function toResponse($request)
    {
        $request->session()->passwordConfirmed();

        return $request->wantsJson()
            ? parent::toResponse($request)
            : redirect()->route('two-factor.setup');
    }
}
