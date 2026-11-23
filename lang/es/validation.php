<?php

return [
    'required' => 'Completa el campo :attribute.',
    'email' => 'Introduce un correo electrónico válido.',
    'unique' => 'Ya existe una cuenta con ese correo electrónico.',
    'confirmed' => 'Las contraseñas deben coincidir.',
    'digits' => 'Introduce un código de :digits dígitos.',
    'string' => 'El campo :attribute debe ser texto.',
    'max' => ['string' => 'El campo :attribute admite hasta :max caracteres.'],
    'min' => ['string' => 'El campo :attribute debe tener al menos :min caracteres.'],
    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'code' => 'código',
        'recovery_code' => 'código de recuperación',
    ],
];
