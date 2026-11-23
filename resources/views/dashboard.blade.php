@extends('layout')
@section('title', 'Hola, '.auth()->user()->name)
@section('content')
    <dl class="server">
        <dt>Servidor que atendió esta petición</dt>
        <dd><code>{{ $hostname ?: 'Hostname no disponible' }}</code></dd>
    </dl>
    <a href="{{ route('dashboard') }}">Actualizar servidor</a>
    @include('auth.logout')
@endsection
