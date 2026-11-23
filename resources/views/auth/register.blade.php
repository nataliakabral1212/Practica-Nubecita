@extends('layout')
@section('title', 'Crear cuenta')
@section('content')
    <p>Después del registro, vincula tu aplicación de autenticación.</p>
    <form method="post" action="{{ route('register.store') }}">
        @csrf
        <label for="name">Nombre</label>
        <input id="name" name="name" autocomplete="name" value="{{ old('name') }}" maxlength="255" required>
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" maxlength="255" required>
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" aria-describedby="password-help" required>
        <small id="password-help">Usa al menos 12 caracteres.</small>
        <label for="password_confirmation">Repite la contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        <button type="submit">Crear cuenta</button>
    </form>
    <p class="footer">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
@endsection
