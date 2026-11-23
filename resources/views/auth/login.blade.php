@extends('layout')
@section('title', 'Iniciar sesión')
@section('content')
    <form method="post" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" required>
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit">Continuar</button>
    </form>
    <p class="footer">¿Es tu primera vez? <a href="{{ route('register') }}">Crea una cuenta</a></p>
@endsection
