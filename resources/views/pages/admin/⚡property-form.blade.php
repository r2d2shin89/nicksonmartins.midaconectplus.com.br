<?php

use App\Concerns\HandlesPropertyPhotos;
use App\Concerns\ValidatesProperty;
use App\Models\Property;
use Flux\Flux;
use Illuminate\Support\Arr;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Imóvel')] class extends Component
{
    use HandlesPropertyPhotos, ValidatesProperty, WithFileUploads;

    public ?Property $property = null;

    public string $title = '';
    public string $purpose = 'venda';
    public string $type = 'Apartamento';
    public string $status = 'disponivel';
    public string $price = '';
    public string $condo_fee = '';
    public string $iptu = '';
    public string $area = '';
    public string $lot_area = '';
    public string $bedrooms = '';
    public string $suite = '';
    public string $bathrooms = '';
    public string $parking = '';
    public string $features = '';
    public string $description = '';
    public string $zip_code = '';
    public string $address = '';
    public string $neighborhood = '';
    public string $city = '';
    public string $state = 'SP';
    public string $latitude = '';
    public string $longitude = '';
    public bool $featured = false;

    /** @var array<int, string> */
    public array $existingPhotos = [];

    /** @var array<int, string> */
    public array $removedPhotos = [];

    /** @var array<int, mixed> */
    public array $newPhotos = [];

    public string $photoUrl = '';

    public function mount(?Property $property = null): void
    {
        $this->property = $property;

        if ($property === null) {
            return;
        }

        $this->title = $property->title;
        $this->purpose = (string) $property->purpose;
        $this->type = (string) $property->type;
        $this->status = (string) ($property->status ?? 'disponivel');
        $this->price = (string) $property->price;
        $this->condo_fee = (string) ($property->condo_fee ?? '');
        $this->iptu = (string) ($property->iptu ?? '');
        $this->area = (string) ($property->area ?? '');
        $this->lot_area = (string) ($property->lot_area ?? '');
        $this->bedrooms = (string) ($property->bedrooms ?? '');
        $this->suite = (string) ($property->suite ?? '');
        $this->bathrooms = (string) ($property->bathrooms ?? '');
        $this->parking = (string) ($property->parking ?? '');
        $this->features = is_array($property->features)
            ? implode("\n", $property->features)
            : (string) ($property->features ?? '');
        $this->description = (string) ($property->description ?? '');
        $this->zip_code = (string) ($property->zip_code ?? '');
        $this->address = (string) ($property->address ?? '');
        $this->neighborhood = (string) ($property->neighborhood ?? '');
        $this->city = (string) ($property->city ?? '');
        $this->state = (string) ($property->state ?? 'SP');
        $this->latitude = (string) ($property->latitude ?? '');
        $this->longitude = (string) ($property->longitude ?? '');
        $this->featured = (bool) ($property->featured ?? false);
        $this->existingPhotos = is_array($property->photos)
            ? array_values(array_filter($property->photos, 'is_string'))
            : [];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = Arr::except($this->propertyRules($this->property === null), [
            'photo',
            'photos',
            'photos.*',
            'photo_url',
            'photo_urls',
            'photo_urls.*',
            'existing_photos',
            'existing_photos.*',
            'remove_photos',
            'remove_photos.*',
            'removed_photos',
            'removed_photos.*',
        ]);

        $rules['newPhotos'] = ['nullable', 'array'];
        $rules['newPhotos.*'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
        $rules['photoUrl'] = ['nullable', 'string', 'max:2048', $this->photoLocationRule()];

        return $rules;
    }

    public function addPhotoUrl(): void
    {
        $photoUrl = trim($this->photoUrl);

        if ($photoUrl === '') {
            return;
        }

        if (! self::isValidPhotoLocation($photoUrl)) {
            $this->addError('photoUrl', __('Informe uma URL http(s) válida ou um caminho /uploads/...'));

            return;
        }

        if (! in_array($photoUrl, $this->existingPhotos, true)) {
            $this->existingPhotos[] = $photoUrl;
        }

        $this->photoUrl = '';
        $this->resetValidation('photoUrl');
    }

    public function removeExistingPhoto(string $photo): void
    {
        if (! in_array($photo, $this->removedPhotos, true)) {
            $this->removedPhotos[] = $photo;
        }

        $this->existingPhotos = array_values(array_filter(
            $this->existingPhotos,
            fn (string $existingPhoto): bool => $existingPhoto !== $photo,
        ));
    }

    public function removeNewPhoto(int $index): void
    {
        unset($this->newPhotos[$index]);

        $this->newPhotos = array_values($this->newPhotos);
    }

    public function save(): void
    {
        $validated = $this->validate();
        $creating = $this->property === null;
        $attributes = $this->propertyAttributes($validated);

        if ($creating) {
            $defaults = [
                'condo_fee' => 0,
                'iptu' => 0,
                'area' => 0,
                'bedrooms' => 0,
                'suite' => 0,
                'bathrooms' => 0,
                'parking' => 0,
                'address' => '',
                'city' => '',
                'state' => '',
                'description' => '',
            ];

            foreach ($defaults as $field => $default) {
                $attributes[$field] ??= $default;
            }
        }

        $attributes['featured'] = (bool) ($attributes['featured'] ?? false);
        $attributes['status'] = $attributes['status'] ?? 'disponivel';
        $attributes['features'] = $this->featureList($attributes['features'] ?? null);
        $attributes['photos'] = $this->resolvePhotoFiles(
            $this->existingPhotos,
            $this->removedPhotos,
            $this->newPhotos,
            $this->photoUrl,
        );

        if ($creating) {
            Property::create($attributes);
        } else {
            $this->property->update($attributes);
        }

        Flux::toast(
            variant: 'success',
            text: $creating ? __('Imóvel criado com sucesso.') : __('Imóvel atualizado com sucesso.'),
        );

        $this->redirectRoute('admin.properties.index', navigate: true);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ $property ? __('Editar imóvel') : __('Novo imóvel') }}</flux:heading>
            <flux:text>
                {{ $property ? __('Atualize as informações do anúncio.') : __('Preencha os dados para cadastrar um novo imóvel.') }}
            </flux:text>
        </div>

        <flux:button variant="ghost" :href="route('admin.properties.index')" wire:navigate icon="arrow-left">
            {{ __('Voltar para imóveis') }}
        </flux:button>
    </div>

    <form wire:submit="save" class="flex flex-col gap-6">
        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Informações básicas') }}</flux:heading>

            <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <flux:input wire:model="title" :label="__('Título do anúncio')" required class="lg:col-span-3" />

                <flux:select wire:model="purpose" :label="__('Finalidade')">
                    <flux:select.option value="venda">{{ __('Venda') }}</flux:select.option>
                    <flux:select.option value="aluguel">{{ __('Aluguel') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model="type" :label="__('Tipo')">
                    @foreach (['Apartamento', 'Casa', 'Cobertura', 'Comercial', 'Terreno', 'Studio'] as $typeOption)
                        <flux:select.option :value="$typeOption">{{ $typeOption }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="status" :label="__('Status')">
                    <flux:select.option value="disponivel">{{ __('Disponível') }}</flux:select.option>
                    <flux:select.option value="reservado">{{ __('Reservado') }}</flux:select.option>
                    <flux:select.option value="vendido">{{ __('Vendido') }}</flux:select.option>
                </flux:select>

                <flux:input wire:model="price" :label="__('Valor (R$)')" type="number" min="0" step="0.01" required />
                <flux:input wire:model="condo_fee" :label="__('Condomínio (R$)')" type="number" min="0" step="0.01" />
                <flux:input wire:model="iptu" :label="__('IPTU (R$)')" type="number" min="0" step="0.01" />
            </div>

            <flux:checkbox
                wire:model="featured"
                :label="__('Destacar este imóvel no portfólio')"
                class="mt-4"
            />
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Dimensões e características') }}</flux:heading>

            <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <flux:input wire:model="area" :label="__('Área útil (m²)')" type="number" min="0" step="0.01" />
                <flux:input wire:model="lot_area" :label="__('Área do terreno (m²)')" type="number" min="0" step="0.01" />
                <flux:input wire:model="bedrooms" :label="__('Quartos')" type="number" min="0" />
                <flux:input wire:model="suite" :label="__('Suítes')" type="number" min="0" />
                <flux:input wire:model="bathrooms" :label="__('Banheiros')" type="number" min="0" />
                <flux:input wire:model="parking" :label="__('Vagas de garagem')" type="number" min="0" />

                <flux:textarea
                    wire:model="features"
                    :label="__('Diferenciais (um por linha)')"
                    :placeholder="__('Piscina\nVaranda gourmet\nPortaria 24h')"
                    class="md:col-span-2"
                />

                <flux:textarea
                    wire:model="description"
                    :label="__('Descrição')"
                    :placeholder="__('Descreva o imóvel, os ambientes e a localização...')"
                    class="md:col-span-3"
                />
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Localização') }}</flux:heading>

            <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <flux:input wire:model="zip_code" :label="__('CEP')" placeholder="01310-200" />
                <flux:input wire:model="address" :label="__('Endereço')" placeholder="{{ __('Rua, número') }}" />
                <flux:input wire:model="neighborhood" :label="__('Bairro')" placeholder="Bela Vista" />
                <flux:input wire:model="city" :label="__('Cidade')" placeholder="São Paulo" />
                <flux:input wire:model="state" :label="__('Estado (UF)')" maxlength="2" placeholder="SP" />
                <flux:input wire:model="latitude" :label="__('Latitude')" placeholder="-23.5505" />
                <flux:input wire:model="longitude" :label="__('Longitude')" placeholder="-46.6333" />
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Fotos do imóvel') }}</flux:heading>
            <flux:text class="mt-1 text-sm">{{ __('JPG, PNG ou WEBP · até 4 MB cada.') }}</flux:text>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label class="flex min-h-32 cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-zinc-300 p-4 text-center text-sm text-zinc-500 hover:border-zinc-400 dark:border-zinc-600 dark:text-zinc-400">
                    <flux:icon name="photo" class="size-6" />
                    <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ __('Selecionar fotos') }}</span>
                    <input wire:model="newPhotos" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only">
                </label>

                @foreach ($newPhotos as $index => $newPhoto)
                    <div class="relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                        @if (is_object($newPhoto) && method_exists($newPhoto, 'temporaryUrl'))
                            <img src="{{ $newPhoto->temporaryUrl() }}" alt="{{ __('Prévia da foto') }}" class="aspect-square w-full object-cover">
                        @endif

                        <flux:button
                            size="sm"
                            variant="ghost"
                            color="red"
                            icon="x-mark"
                            wire:click="removeNewPhoto({{ $index }})"
                            :label="__('Remover foto')"
                            class="absolute top-2 end-2"
                        />
                    </div>
                @endforeach

                @foreach ($existingPhotos as $photo)
                    <div class="relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <img src="{{ $photo }}" alt="{{ __('Prévia do imóvel') }}" class="aspect-square w-full object-cover">

                        <flux:button
                            size="sm"
                            variant="ghost"
                            color="red"
                            icon="x-mark"
                            wire:click="removeExistingPhoto(@js($photo))"
                            :label="__('Remover foto')"
                            class="absolute top-2 end-2"
                        />
                    </div>
                @endforeach
            </div>

            @error('newPhotos.*')
                <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
            @enderror

            <div class="mt-4 flex flex-wrap items-end gap-3">
                <flux:input
                    wire:model="photoUrl"
                    :label="__('Adicionar foto por URL')"
                    type="url"
                    placeholder="https://..."
                    class="min-w-64 flex-1"
                />

                <flux:button type="button" wire:click="addPhotoUrl" variant="outline" icon="plus">
                    {{ __('Adicionar') }}
                </flux:button>
            </div>

            @error('photoUrl')
                <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
            @enderror
        </section>

        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <flux:button variant="ghost" :href="route('admin.properties.index')" wire:navigate>
                {{ __('Cancelar') }}
            </flux:button>

            <flux:button variant="primary" type="submit" icon="check">
                {{ $property ? __('Salvar alterações') : __('Salvar imóvel') }}
            </flux:button>
        </div>
    </form>
</div>
