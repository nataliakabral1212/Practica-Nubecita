@extends('layout')
@section('title', 'Activa la verificación en dos pasos')
@section('content')
    <p>Escanea este QR con Google Authenticator o introduce la clave manualmente.</p>
    <div class="qr" role="img" aria-label="Código QR para configurar el segundo factor">
        {!! $user->twoFactorQrCodeSvg() !!}
    </div>
    <p class="secret"><strong>Clave de configuración</strong><br><code>{{ decrypt($user->two_factor_secret) }}</code></p>
    <form method="post" action="{{ route('totp.confirm') }}">
        @csrf
        @include('auth.code-field')
        <button type="submit">Confirmar y activar</button>
    </form>
    @include('auth.logout')
@endsection
