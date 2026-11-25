<?php

use App\Models\Message;
use App\Models\Property;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    #[Computed]
    public function propertyCount(): int
    {
        return Property::count();
    }

    #[Computed]
    public function messageCount(): int
    {
        return Message::count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Property>
     */
    #[Computed]
    public function recentProperties()
    {
        return Property::latest()->limit(5)->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Message>
     */
    #[Computed]
    public function recentMessages()
    {
        return Message::latest()->limit(5)->get();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
            <flux:text>{{ __('Visão geral do portfólio e dos contatos recebidos.') }}</flux:text>
        </div>

        <flux:button variant="primary" :href="route('admin.properties.create')" wire:navigate icon="plus">
            {{ __('Novo imóvel') }}
        </flux:button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:text class="text-sm">{{ __('Total de imóveis') }}</flux:text>
            <flux:heading size="2xl" class="mt-2">{{ $this->propertyCount }}</flux:heading>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:text class="text-sm">{{ __('Mensagens recebidas') }}</flux:text>
            <flux:heading size="2xl" class="mt-2">{{ $this->messageCount }}</flux:heading>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between gap-4 border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
            <div>
                <flux:heading size="lg">{{ __('Imóveis recentes') }}</flux:heading>
                <flux:text class="text-sm">{{ __('Últimos anúncios cadastrados') }}</flux:text>
            </div>

            <flux:link :href="route('admin.properties.index')" wire:navigate>
                {{ __('Ver todos') }} →
            </flux:link>
        </div>

        @if ($this->recentProperties->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Imóvel') }}</th>
                            <th class="px-6 py-3">{{ __('Localização') }}</th>
                            <th class="px-6 py-3">{{ __('Finalidade') }}</th>
                            <th class="px-6 py-3">{{ __('Valor') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3"><span class="sr-only">{{ __('Ações') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->recentProperties as $property)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $property->first_photo }}" alt="" class="size-12 rounded-lg object-cover">
                                        <div>
                                            <div class="font-medium">{{ $property->title }}</div>
                                            <flux:text class="text-xs">
                                                {{ $property->type }}{{ $property->featured ? ' · ★ Destaque' : '' }}
                                            </flux:text>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>{{ $property->neighborhood ?: '—' }}</div>
                                    <flux:text class="text-xs">{{ $property->city }}/{{ $property->state }}</flux:text>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge size="sm" :color="$property->purpose === 'aluguel' ? 'blue' : 'green'">
                                        {{ $property->purpose === 'aluguel' ? __('Aluguel') : __('Venda') }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 font-semibold">{{ $property->formatted_price }}</td>
                                <td class="px-6 py-4">
                                    <flux:badge size="sm" :color="$property->status === 'disponivel' ? 'green' : 'zinc'">
                                        {{ match ($property->status) {
                                            'disponivel' => __('Disponível'),
                                            'reservado' => __('Reservado'),
                                            'vendido' => __('Vendido'),
                                            default => (string) $property->status,
                                        } }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-end">
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="pencil"
                                        :href="route('admin.properties.edit', $property)"
                                        :label="__('Editar :name', ['name' => $property->title])"
                                        wire:navigate
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6">
                <flux:heading size="md">{{ __('Nenhum imóvel cadastrado') }}</flux:heading>
                <flux:text>{{ __('Comece cadastrando o primeiro imóvel.') }}</flux:text>
            </div>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between gap-4 border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
            <div>
                <flux:heading size="lg">{{ __('Mensagens recentes') }}</flux:heading>
                <flux:text class="text-sm">{{ __('Últimos contatos enviados pelo site') }}</flux:text>
            </div>

            <flux:link :href="route('admin.messages.index')" wire:navigate>
                {{ __('Ver todas') }} →
            </flux:link>
        </div>

        @if ($this->recentMessages->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Remetente') }}</th>
                            <th class="px-6 py-3">{{ __('Assunto') }}</th>
                            <th class="px-6 py-3">{{ __('Mensagem') }}</th>
                            <th class="px-6 py-3">{{ __('Data') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->recentMessages as $message)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ $message->name }}</div>
                                    <flux:text class="text-xs">
                                        {{ $message->email }}{{ $message->phone ? ' · '.$message->phone : '' }}
                                    </flux:text>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge size="sm">{{ $message->subject }}</flux:badge>
                                </td>
                                <td class="max-w-md px-6 py-4">
                                    <flux:text class="line-clamp-2">{{ $message->message }}</flux:text>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ $message->created_at?->format('d/m/Y H:i') ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6">
                <flux:heading size="md">{{ __('Nenhuma mensagem ainda') }}</flux:heading>
                <flux:text>{{ __('As mensagens enviadas pelo site aparecerão aqui.') }}</flux:text>
            </div>
        @endif
    </div>
</div>
