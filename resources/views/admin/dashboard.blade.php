@extends('admin.layout')

@section('title', 'Dashboard | Admin')

@section('content')
@php
    $statusLabel = fn ($status) => match ($status) {
        'disponivel' => 'Disponível',
        'reservado' => 'Reservado',
        'vendido' => 'Vendido',
        default => (string) $status,
    };
@endphp

<div class="admin-top">
    <div>
        <h1>Dashboard</h1>
        <p>Visão geral do portfólio e dos contatos recebidos.</p>
    </div>
    <a href="{{ route('admin.properties.create') }}" class="btn btn-blue">+ Novo imóvel</a>
</div>

@if ($errors->any())
    <div class="admin-alert error">{{ $errors->first() }}</div>
@endif
@if (session('status'))
    <div class="admin-alert ok">{{ session('status') }}</div>
@endif

<div class="admin-cards">
    <div class="admin-card"><span>Total de imóveis</span><strong>{{ $propertyCount }}</strong></div>
    <div class="admin-card"><span>Mensagens recebidas</span><strong>{{ $messageCount }}</strong></div>
</div>

<div class="admin-panel">
    <div class="panel-head">
        <div><h2>Imóveis recentes</h2><p>Últimos anúncios cadastrados</p></div>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-blue">Ver todos →</a>
    </div>
    @if (count($recentProperties) > 0)
        <table class="admin-table">
            <thead>
                <tr><th>Imóvel</th><th>Localização</th><th>Finalidade</th><th>Valor</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($recentProperties as $property)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img class="admin-thumb" src="{{ $property->first_photo ?: '/no-image.svg' }}" alt="">
                                <div>
                                    <div class="admin-title">{{ $property->title }}</div>
                                    <div class="admin-sub">{{ $property->type }}{{ $property->featured ? ' · ★ Destaque' : '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><div>{{ $property->neighborhood ?: '—' }}</div><div class="admin-sub">{{ $property->city }}/{{ $property->state }}</div></td>
                        <td><span class="admin-tag {{ $property->purpose === 'aluguel' ? 'rent' : 'sale' }}">{{ $property->purpose === 'aluguel' ? 'Aluguel' : 'Venda' }}</span></td>
                        <td style="font-weight: 700; color: var(--blue-800);">{{ $property->formatted_price }}</td>
                        <td><span class="admin-tag {{ $property->status === 'disponivel' ? 'on' : 'off' }}">{{ $statusLabel($property->status) }}</span></td>
                        <td><a href="{{ route('admin.properties.edit', $property->id) }}" class="icon-btn" aria-label="Editar {{ $property->title }}">✎</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-admin"><h3>Nenhum imóvel cadastrado</h3><p>Comece cadastrando o primeiro imóvel.</p></div>
    @endif
</div>

<div class="admin-panel" style="margin-top: 28px;">
    <div class="panel-head">
        <div><h2>Mensagens recentes</h2><p>Últimos contatos enviados pelo site</p></div>
        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-blue">Ver todas →</a>
    </div>
    @if (count($recentMessages) > 0)
        <table class="admin-table">
            <thead>
                <tr><th>Remetente</th><th>Assunto</th><th>Mensagem</th><th>Data</th></tr>
            </thead>
            <tbody>
                @foreach ($recentMessages as $message)
                    <tr>
                        <td><div class="admin-title">{{ $message->name }}</div><div class="admin-sub">{{ $message->email }}{{ $message->phone ? ' · ' . $message->phone : '' }}</div></td>
                        <td><span class="admin-tag sale">{{ $message->subject }}</span></td>
                        <td><div class="msg-text">{{ $message->message }}</div></td>
                        <td class="msg-date">{{ $message->created_at ? $message->created_at->format('d/m/Y H:i') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-admin"><h3>Nenhuma mensagem ainda</h3><p>As mensagens enviadas pelo site aparecerão aqui.</p></div>
    @endif
</div>
@endsection
