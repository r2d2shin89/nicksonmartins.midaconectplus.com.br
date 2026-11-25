<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Área administrativa | {{ config('broker.name', 'Nickson Martins') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $svg = fn (string $path, int $size = 18) => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
@endphp

<div class="admin-login-page">
    <div class="login-card">
        <div class="login-mark">{!! $svg('<circle cx="8" cy="15" r="4"/><path d="m11 12 9-9 2 2-2 2 2 2-3 3-2-2-2 2"/>', 27) !!}</div>
        <h1>Área administrativa</h1>
        <p>Acesso restrito para gerenciamento de imóveis e mensagens.</p>

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <div class="form-group">
                <label for="password">Senha de acesso</label>
                <input id="password" name="password" type="password" required autofocus autocomplete="current-password" placeholder="Digite a senha">
            </div>

            @if ($errors->any())
                <div class="admin-alert error">{{ $errors->first() }}</div>
            @elseif (session('error'))
                <div class="admin-alert error">{{ session('error') }}</div>
            @endif

            @if (session('status'))
                <div class="admin-alert ok">{{ session('status') }}</div>
            @endif

            <button type="submit" class="btn btn-blue" style="width: 100%;">
                {!! $svg('<path d="m5 12 4 4L19 6"/>', 17) !!} Entrar
            </button>
        </form>

        <div class="login-hint">Senha padrão de demonstração: <code>admin123</code>. Altere no arquivo <code>.env</code> antes de publicar.</div>
        <a href="{{ route('home') }}" class="back-link">{!! $svg('<path d="M19 12H5M11 18l-6-6 6-6"/>', 15) !!} Voltar para o site</a>
    </div>
</div>
</body>
</html>
