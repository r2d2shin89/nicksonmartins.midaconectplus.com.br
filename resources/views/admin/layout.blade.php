<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin | ' . config('broker.name', 'Nickson Martins'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .admin-nav a {
            display: flex; align-items: center; gap: 12px; width: 100%;
            padding: 13px 16px; border-radius: 12px; color: rgba(255,255,255,.72);
            font-weight: 600; font-size: 14px; transition: background .2s, color .2s;
        }
        .admin-nav a:hover { background: rgba(255,255,255,.08); color: white; }
        .admin-nav a.active { background: var(--blue-700); color: white; box-shadow: 0 10px 24px rgba(0,77,193,.35); }
        @media (max-width: 800px) {
            .admin-nav a span:not(.count) { display: none; }
            .admin-nav a { justify-content: center; padding: 13px; }
        }
    </style>
</head>
<body>
@php
    $svg = fn (string $path, int $size = 18) => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
    $icons = [
        'building' => $svg('<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M16 10h.01M8 10h.01M8 14h.01M12 14h.01M16 14h.01"/>', 22),
        'dashboard' => $svg('<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>'),
        'home' => $svg('<path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22v-6h6v6"/>'),
        'mail' => $svg('<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 6 10 7 10-7"/>'),
        'external' => $svg('<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>'),
        'logout' => $svg('<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>', 17),
    ];
@endphp

<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">{!! $icons['building'] !!}</span>
            <span><span class="brand-name">Admin</span><span class="brand-sub">{{ config('broker.name', 'Nickson Martins') }}</span></span>
        </a>

        <nav class="admin-nav" aria-label="Navegação administrativa">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                {!! $icons['dashboard'] !!}<span>Dashboard</span>
            </a>
            <a href="{{ route('admin.properties.index') }}" class="{{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
                {!! $icons['home'] !!}<span>Imóveis</span>
            </a>
            <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                {!! $icons['mail'] !!}<span>Mensagens</span>
            </a>
            <a href="{{ route('home') }}" target="_blank" rel="noreferrer">
                {!! $icons['external'] !!}<span>Ver site</span>
            </a>
        </nav>

        <div class="admin-sidebar-bottom">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">{!! $icons['logout'] !!}<span>Sair da conta</span></button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        @yield('content')
    </main>
</div>
</body>
</html>
