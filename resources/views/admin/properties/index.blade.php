@extends('admin.layout')

@section('title', 'Imóveis | Admin')

@section('content')
@php
    $svg = fn (string $path, int $size = 16) => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
    $statusLabel = fn ($status) => match ($status) {
        'disponivel' => 'Disponível',
        'reservado' => 'Reservado',
        'vendido' => 'Vendido',
        default => (string) $status,
    };
@endphp

<div class="admin-top">
    <div>
        <h1>Gerenciar imóveis</h1>
        <p>Cadastre, edite e organize seu portfólio.</p>
    </div>
    <a href="{{ route('admin.properties.create') }}" class="btn btn-blue">+ Novo imóvel</a>
</div>

@if ($errors->any())
    <div class="admin-alert error">{{ $errors->first() }}</div>
@endif
@if (session('status'))
    <div class="admin-alert ok">{{ session('status') }}</div>
@endif

<div class="admin-panel">
    <div class="panel-head">
        <div><h2>Imóveis cadastrados</h2><p>{{ $properties->total() }} imóvel(is) no portfólio</p></div>
    </div>

    @if ($properties->isEmpty())
        <div class="empty-admin"><h3>Nenhum imóvel cadastrado</h3><p>Comece cadastrando o primeiro imóvel.</p></div>
    @else
        <table class="admin-table">
            <thead>
                <tr><th>Imóvel</th><th>Localização</th><th>Finalidade</th><th>Área</th><th>Valor</th><th>Status</th><th>Ações</th></tr>
            </thead>
            <tbody>
                @foreach ($properties as $property)
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
                        <td>{{ $property->formatted_area }}</td>
                        <td style="font-weight: 700; color: var(--blue-800);">{{ $property->formatted_price }}</td>
                        <td><span class="admin-tag {{ $property->status === 'disponivel' ? 'on' : 'off' }}">{{ $statusLabel($property->status) }}</span></td>
                        <td>
                            <div class="admin-actions">
                                <a href="{{ route('admin.properties.edit', $property->id) }}" class="icon-btn" aria-label="Editar {{ $property->title }}">{!! $svg('<path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>') !!}</a>
                                <form method="POST" action="{{ route('admin.properties.destroy', $property->id) }}" onsubmit="return confirm({{ json_encode('Excluir o imóvel "' . $property->title . '"? Essa ação não pode ser desfeita.') }});">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn danger" aria-label="Excluir {{ $property->title }}">{!! $svg('<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/>') !!}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if ($properties->hasPages())
    <div class="admin-panel" style="margin-top: 24px; padding: 18px 24px;">
        {{ $properties->links() }}
    </div>
@endif
@endsection
