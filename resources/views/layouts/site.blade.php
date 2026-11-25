<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#072ab5">
    <title>@yield('title', config('broker.name', 'Nickson Martins') . ' | Corretor de Imóveis')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body data-whatsapp="{{ config('broker.whatsapp') }}">
@php
    $icons = [
        'building' => '<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M16 10h.01M8 10h.01M8 14h.01M12 14h.01M16 14h.01"/></svg>',
        'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>',
        'menu' => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>',
        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></svg>',
        'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
        'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6ZM2 9h4v12H2zM4 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4Z"/></svg>',
        'youtube' => '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 8a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4z"/><path d="m10 9 5 3-5 3z"/></svg>',
        'whatsapp' => '<svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.9 9.9 0 0 1-3-.4L3 21l1.6-5a8 8 0 0 1-.6-4.4 8.4 8.4 0 0 1 8.4-8.4 8.4 8.4 0 0 1 8.6 8.3Z"/><path d="M8.5 8.5c.3 2 2 4 4 4l1.5-1.5 2 .8c.5 1-.2 2-1 2.4a7 7 0 0 1-3 .5 7 7 0 0 1-4.2-1.4 7 7 0 0 1-2-3.4c.2-.9 1.4-1.8 2.7-1.8Z"/></svg>',
    ];
@endphp

<header class="site-header">
    <div class="container header-inner">
        <a href="#topo" class="brand" aria-label="Voltar ao início">
            <span class="brand-mark">{!! $icons['building'] !!}</span>
            <span>
                <span class="brand-name">{{ config('broker.name', 'Nickson Martins') }}</span>
                <span class="brand-sub">&nbsp;Imóveis de elite</span>
            </span>
        </a>

        <nav class="nav-links" id="site-nav">
            <a href="#sobre">Sobre mim</a>
            <a href="#imoveis">Imóveis</a>
            <a href="#diferenciais">Diferenciais</a>
            <a href="#contato">Contato</a>
            <a href="{{ route('login') }}">Área administrativa</a>
        </nav>

        <a href="https://wa.me/{{ config('broker.whatsapp') }}" target="_blank" rel="noreferrer" class="header-cta">
            {!! $icons['phone'] !!} Falar com o corretor
        </a>

        <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="site-nav">
            {!! $icons['menu'] !!}
        </button>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="brand">
                    <span class="brand-mark">{!! $icons['building'] !!}</span>
                    <span>
                        <span class="brand-name">{{ config('broker.name', 'Nickson Martins') }}</span>
                        <span class="brand-sub">Imóveis de elite</span>
                    </span>
                </div>
                <p>Corretagem imobiliário com ética, conhecimento e resultado. Seu próximo endereço começa aqui.</p>
                <div class="footer-socials">
                    <a href="{{ config('broker.social.instagram') }}" target="_blank" rel="noreferrer" aria-label="Instagram">{!! $icons['instagram'] !!}</a>
                    <a href="{{ config('broker.social.facebook') }}" target="_blank" rel="noreferrer" aria-label="Facebook">{!! $icons['facebook'] !!}</a>
                    <a href="{{ config('broker.social.linkedin') }}" target="_blank" rel="noreferrer" aria-label="LinkedIn">{!! $icons['linkedin'] !!}</a>
                    <a href="{{ config('broker.social.youtube') }}" target="_blank" rel="noreferrer" aria-label="YouTube">{!! $icons['youtube'] !!}</a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Navegação</h4>
                <ul>
                    <li><a href="#topo">Início</a></li>
                    <li><a href="#sobre">Sobre mim</a></li>
                    <li><a href="#imoveis">Imóveis</a></li>
                    <li><a href="#diferenciais">Diferenciais</a></li>
                    <li><a href="#redes">Redes sociais</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contato</h4>
                <ul>
                    <li>{{ config('broker.phone') }}</li>
                    <li>{{ config('broker.email') }}</li>
                    <li>{{ config('broker.city') }}</li>
                    <li>{{ config('broker.role') }}</li>
                    <li style="margin-top: 8px;"><a href="{{ route('login') }}">Área administrativa →</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} {{ config('broker.name', 'Nickson Martins') }}. Todos os direitos reservados.</span>
            <span>CRECI {{ config('broker.creci', '123.456-J') }} · Imagens meramente ilustrativas.</span>
        </div>
    </div>
</footer>

<a href="https://wa.me/{{ config('broker.whatsapp') }}" target="_blank" rel="noreferrer" class="floating-whatsapp" aria-label="Falar no WhatsApp">
    {!! $icons['whatsapp'] !!}
</a>
</body>
</html>
