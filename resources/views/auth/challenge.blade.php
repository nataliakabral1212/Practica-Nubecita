@extends('layout')
@section('title', 'Verifica tu identidad')
@section('content')
    <p>Introduce el código de seis dígitos de tu aplicación de autenticación.</p>
    <form method="post" action="{{ route('two-factor.login.store') }}">
        @csrf
        @include('auth.code-field')
        <button type="submit">Iniciar sesión</button>
    </form>
    <details>
        <summary>Usar un código de recuperación</summary>
        <form method="post" action="{{ route('two-factor.login.store') }}">
            @csrf
            <label for="recovery_code">Código de recuperación</label>
            <input id="recovery_code" name="recovery_code" type="password" autocomplete="off" required>
            <button type="submit">Recuperar acceso</button>
        </form>
    </details>
@endsection
