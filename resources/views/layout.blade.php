<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    @vite('resources/css/app.css')
</head>
<body>
    <main class="shell">
        <header class="brand">
            <img src="{{ asset('images/servidores.png') }}" width="40" height="40" alt="">
            <span>{{ config('app.name') }}</span>
        </header>
        <section class="card" aria-labelledby="titulo">
            <h1 id="titulo">@yield('title')</h1>
            @php($messages = array_merge($errors->all(), $errors->getBag('confirmTwoFactorAuthentication')->all()))
            @if ($messages)
                <ul class="errors" role="alert">
                    @foreach ($messages as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            @yield('content')
        </section>
    </main>
</body>
</html>
