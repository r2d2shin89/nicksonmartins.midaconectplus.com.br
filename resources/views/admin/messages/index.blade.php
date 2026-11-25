@extends('admin.layout')

@section('title', 'Mensagens | Admin')

@section('content')
@php
    $svg = fn (string $path, int $size = 16) => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
@endphp

<div class="admin-top">
    <div>
        <h1>Mensagens recebidas</h1>
        <p>Contatos enviados pelo formulário do site.</p>
    </div>
</div>

@if ($errors->any())
    <div class="admin-alert error">{{ $errors->first() }}</div>
@endif
@if (session('status'))
    <div class="admin-alert ok">{{ session('status') }}</div>
@endif

<div class="admin-panel">
    <div class="panel-head">
        <div><h2>Mensagens</h2><p>{{ $messages->total() }} mensagem(ns) recebida(s)</p></div>
    </div>

    @if ($messages->isEmpty())
        <div class="empty-admin"><h3>Nenhuma mensagem ainda</h3><p>As mensagens enviadas pelo site aparecerão aqui.</p></div>
    @else
        <table class="admin-table">
            <thead>
                <tr><th>Remetente</th><th>Assunto</th><th>Mensagem</th><th>Data</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($messages as $message)
                    <tr>
                        <td>
                            <div class="admin-title">{{ $message->name }}</div>
                            <div class="admin-sub">{{ $message->email }}{{ $message->phone ? ' · ' . $message->phone : '' }}</div>
                        </td>
                        <td><span class="admin-tag sale">{{ $message->subject }}</span></td>
                        <td><div class="msg-text">{{ $message->message }}</div></td>
                        <td class="msg-date">{{ $message->created_at ? $message->created_at->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.messages.destroy', $message->id) }}" onsubmit="return confirm({{ json_encode('Excluir esta mensagem?') }});">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn danger" aria-label="Excluir mensagem">{!! $svg('<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/>') !!}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if ($messages->hasPages())
    <div class="admin-panel" style="margin-top: 24px; padding: 18px 24px;">
        {{ $messages->links() }}
    </div>
@endif
@endsection
