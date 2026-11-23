<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        $attributes = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['bail', 'required', 'string', 'confirmed', Password::min(12), function (string $attribute, string $value, \Closure $fail): void {
                if (strlen($value) > 72 || str_contains($value, "\0")) {
                    $fail('La contraseña supera los 72 bytes permitidos o contiene caracteres no válidos.');
                }
            }],
        ])->validate();

        return DB::transaction(function () use ($attributes): User {
            $user = User::create($attributes);
            app(EnableTwoFactorAuthentication::class)($user);

            return $user;
        });
    }
}
