<?php

use App\Http\Middleware\ProtectFortifyRoutes;
use Laravel\Fortify\Features;

return [
    'guard' => 'web',
    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,
    'home' => '/inicio',
    'middleware' => ['web', ProtectFortifyRoutes::class],
    'views' => true,
    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],
    'features' => [
        Features::registration(),
        Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => false]),
    ],
];
