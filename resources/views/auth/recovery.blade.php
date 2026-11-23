@extends('layout')
@section('title', 'Guarda tus códigos de recuperación')
@section('content')
    <p>Guárdalos en un lugar seguro. Cada código se puede usar una vez para iniciar sesión si pierdes el acceso a tu aplicación de autenticación.</p>
    <ul class="recovery">
        @foreach ($codes as $code)
            <li><code>{{ $code }}</code></li>
        @endforeach
    </ul>
    <a class="button" href="{{ route('dashboard') }}">Ir al inicio</a>
@endsection
