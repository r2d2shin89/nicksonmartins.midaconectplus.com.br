<?php

use App\Models\Message;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Mensagens')] class extends Component
{
    use WithPagination;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Message>
     */
    #[Computed]
    public function messages()
    {
        return Message::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function delete(Message $message): void
    {
        $message->delete();

        Flux::toast(variant: 'success', text: __('Mensagem removida com sucesso.'));
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Mensagens recebidas') }}</flux:heading>
        <flux:text>{{ __('Contatos enviados pelo formulário do site.') }}</flux:text>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('Mensagens') }}</flux:heading>
            <flux:text class="text-sm">
                {{ __(':count mensagem(ns) recebida(s)', ['count' => $this->messages->total()]) }}
            </flux:text>
        </div>

        @if ($this->messages->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Remetente') }}</th>
                            <th class="px-6 py-3">{{ __('Assunto') }}</th>
                            <th class="px-6 py-3">{{ __('Mensagem') }}</th>
                            <th class="px-6 py-3">{{ __('Data') }}</th>
                            <th class="px-6 py-3"><span class="sr-only">{{ __('Ações') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->messages as $message)
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
                                <td class="px-6 py-4 text-end">
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        color="red"
                                        icon="trash"
                                        wire:click="delete({{ $message->id }})"
                                        wire:confirm="{{ __('Excluir esta mensagem?') }}"
                                        :label="__('Excluir mensagem')"
                                    />
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

    @if ($this->messages->hasPages())
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            {{ $this->messages->links() }}
        </div>
    @endif
</div>
