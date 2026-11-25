<?php

use App\Concerns\HandlesPropertyPhotos;
use App\Models\Property;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Imóveis')] class extends Component
{
    use HandlesPropertyPhotos, WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Property>
     */
    #[Computed]
    public function properties()
    {
        $search = trim($this->search);

        return Property::query()
            ->when($search !== '', function ($query) use ($search): void {
                $term = '%'.$search.'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('title', 'like', $term)
                        ->orWhere('city', 'like', $term)
                        ->orWhere('neighborhood', 'like', $term)
                        ->orWhere('type', 'like', $term);
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12);
    }

    public function delete(Property $property): void
    {
        $photos = $property->photos ?? [];

        $property->delete();

        $this->deleteLocalPhotos(is_array($photos) ? $photos : []);

        Flux::toast(variant: 'success', text: __('Imóvel removido com sucesso.'));
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Gerenciar imóveis') }}</flux:heading>
            <flux:text>{{ __('Cadastre, edite e organize seu portfólio.') }}</flux:text>
        </div>

        <flux:button variant="primary" :href="route('admin.properties.create')" wire:navigate icon="plus">
            {{ __('Novo imóvel') }}
        </flux:button>
    </div>

    <div class="max-w-sm">
        <flux:input
            wire:model.live.debounce.300ms="search"
            :label="__('Buscar imóvel')"
            type="search"
            icon="magnifying-glass"
            :placeholder="__('Título, cidade ou bairro')"
        />
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('Imóveis cadastrados') }}</flux:heading>
            <flux:text class="text-sm">
                {{ __(':count imóvel(is) no portfólio', ['count' => $this->properties->total()]) }}
            </flux:text>
        </div>

        @if ($this->properties->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Imóvel') }}</th>
                            <th class="px-6 py-3">{{ __('Localização') }}</th>
                            <th class="px-6 py-3">{{ __('Finalidade') }}</th>
                            <th class="px-6 py-3">{{ __('Área') }}</th>
                            <th class="px-6 py-3">{{ __('Valor') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Ações') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->properties as $property)
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
                                <td class="whitespace-nowrap px-6 py-4">{{ $property->formatted_area }}</td>
                                <td class="whitespace-nowrap px-6 py-4 font-semibold">{{ $property->formatted_price }}</td>
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
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            icon="pencil"
                                            :href="route('admin.properties.edit', $property)"
                                            :label="__('Editar :name', ['name' => $property->title])"
                                            wire:navigate
                                        />

                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            color="red"
                                            icon="trash"
                                            wire:click="delete({{ $property->id }})"
                                            wire:confirm="{{ __('Excluir o imóvel ":name"? Essa ação não pode ser desfeita.', ['name' => $property->title]) }}"
                                            :label="__('Excluir :name', ['name' => $property->title])"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6">
                <flux:heading size="md">{{ __('Nenhum imóvel encontrado') }}</flux:heading>
                <flux:text>{{ __('Cadastre um imóvel ou ajuste a busca.') }}</flux:text>
            </div>
        @endif
    </div>

    @if ($this->properties->hasPages())
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            {{ $this->properties->links() }}
        </div>
    @endif
</div>
