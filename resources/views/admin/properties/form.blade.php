@extends('admin.layout')

@section('title', ($property ? 'Editar imóvel' : 'Novo imóvel') . ' | Admin')

@section('content')
@php
    $isEdit = isset($property) && $property !== null;

    $field = function (string $key, $default = '') use ($isEdit, $property) {
        $old = old($key);
        if ($old !== null) {
            return is_array($old) ? implode("\n", $old) : $old;
        }
        return $isEdit ? ($property->{$key} ?? $default) : $default;
    };

    $featuresValue = old('features');
    if ($featuresValue === null) {
        $rawFeatures = $isEdit ? ($property->features ?? []) : [];
        $featuresValue = is_array($rawFeatures) ? implode("\n", $rawFeatures) : (string) $rawFeatures;
    } elseif (is_array($featuresValue)) {
        $featuresValue = implode("\n", $featuresValue);
    }

    $photos = [];
    if ($isEdit) {
        $rawPhotos = $property->photos ?? [];
        $photos = is_array($rawPhotos)
            ? $rawPhotos
            : (is_string($rawPhotos) && $rawPhotos !== '' ? (json_decode($rawPhotos, true) ?: []) : []);
    }
    $removedPhotos = old('remove_photos', []);
    $removedPhotos = is_array($removedPhotos) ? $removedPhotos : [];

    $status = (string) $field('status', 'disponivel');
    $purpose = (string) $field('purpose', 'venda');
    $type = (string) $field('type', 'Apartamento');
@endphp

<div class="admin-top">
    <div>
        <h1>{{ $isEdit ? 'Editar imóvel' : 'Novo imóvel' }}</h1>
        <p>{{ $isEdit ? 'Atualize as informações do anúncio.' : 'Preencha os dados para cadastrar um novo imóvel.' }}</p>
    </div>
    <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-blue">← Voltar para imóveis</a>
</div>

@if ($errors->any())
    <div class="admin-alert error">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('status'))
    <div class="admin-alert ok">{{ session('status') }}</div>
@endif

<form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.properties.update', $property->id) : route('admin.properties.store') }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="admin-panel" style="padding: 28px;">
        <div class="form-section">
            <h3>Informações básicas</h3>
            <div class="form-grid">
                <div class="form-group full">
                    <label for="title">Título do anúncio *</label>
                    <input id="title" name="title" required placeholder="Ex.: Apartamento premium com vista panorâmica" value="{{ $field('title') }}">
                </div>
                <div class="form-group">
                    <label for="purpose">Finalidade</label>
                    <select id="purpose" name="purpose">
                        <option value="venda" {{ $purpose === 'venda' ? 'selected' : '' }}>Venda</option>
                        <option value="aluguel" {{ $purpose === 'aluguel' ? 'selected' : '' }}>Aluguel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type">Tipo</label>
                    <select id="type" name="type">
                        @foreach (['Apartamento', 'Casa', 'Cobertura', 'Comercial', 'Terreno', 'Studio'] as $option)
                            <option {{ $type === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="disponivel" {{ $status === 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="reservado" {{ $status === 'reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="vendido" {{ $status === 'vendido' ? 'selected' : '' }}>Vendido</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Valor (R$) *</label>
                    <input id="price" name="price" type="number" min="0" step="0.01" required placeholder="3500000" value="{{ $field('price') }}">
                </div>
                <div class="form-group">
                    <label for="condo_fee">Condomínio (R$)</label>
                    <input id="condo_fee" name="condo_fee" type="number" min="0" step="0.01" placeholder="0" value="{{ $field('condo_fee') }}">
                </div>
                <div class="form-group">
                    <label for="iptu">IPTU (R$)</label>
                    <input id="iptu" name="iptu" type="number" min="0" step="0.01" placeholder="0" value="{{ $field('iptu') }}">
                </div>
            </div>
            <label class="checkbox-row" for="featured">
                <input id="featured" name="featured" type="checkbox" value="1" {{ old('featured', $isEdit ? $property->featured : false) ? 'checked' : '' }}>
                Destacar este imóvel no portfólio
            </label>
        </div>

        <div class="form-section">
            <h3>Dimensões e características</h3>
            <div class="form-grid">
                <div class="form-group"><label for="area">Área útil (m²)</label><input id="area" name="area" type="number" min="0" step="0.01" placeholder="120" value="{{ $field('area') }}"></div>
                <div class="form-group"><label for="lot_area">Área do terreno (m²)</label><input id="lot_area" name="lot_area" type="number" min="0" step="0.01" placeholder="300" value="{{ $field('lot_area') }}"></div>
                <div class="form-group"><label for="bedrooms">Quartos</label><input id="bedrooms" name="bedrooms" type="number" min="0" placeholder="3" value="{{ $field('bedrooms') }}"></div>
                <div class="form-group"><label for="suite">Suítes</label><input id="suite" name="suite" type="number" min="0" placeholder="1" value="{{ $field('suite') }}"></div>
                <div class="form-group"><label for="bathrooms">Banheiros</label><input id="bathrooms" name="bathrooms" type="number" min="0" placeholder="2" value="{{ $field('bathrooms') }}"></div>
                <div class="form-group"><label for="parking">Vagas de garagem</label><input id="parking" name="parking" type="number" min="0" placeholder="2" value="{{ $field('parking') }}"></div>
                <div class="form-group full"><label for="features">Diferenciais (um por linha)</label><textarea id="features" name="features" rows="4" placeholder="Piscina&#10;Varanda gourmet&#10;Portaria 24h">{{ $featuresValue }}</textarea></div>
                <div class="form-group full"><label for="description">Descrição</label><textarea id="description" name="description" rows="5" placeholder="Descreva o imóvel, os ambientes e a localização...">{{ $field('description') }}</textarea></div>
            </div>
        </div>

        <div class="form-section">
            <h3>Localização</h3>
            <div class="form-grid">
                <div class="form-group"><label for="zip_code">CEP</label><input id="zip_code" name="zip_code" placeholder="01310-200" value="{{ $field('zip_code') }}"></div>
                <div class="form-group"><label for="address">Endereço</label><input id="address" name="address" placeholder="Rua, número" value="{{ $field('address') }}"></div>
                <div class="form-group"><label for="neighborhood">Bairro</label><input id="neighborhood" name="neighborhood" placeholder="Bela Vista" value="{{ $field('neighborhood') }}"></div>
                <div class="form-group"><label for="city">Cidade</label><input id="city" name="city" placeholder="São Paulo" value="{{ $field('city') }}"></div>
                <div class="form-group"><label for="state">Estado (UF)</label><input id="state" name="state" maxlength="2" placeholder="SP" value="{{ $field('state', 'SP') }}"></div>
                <div class="form-group"><label for="latitude">Latitude</label><input id="latitude" name="latitude" placeholder="-23.5505" value="{{ $field('latitude') }}"></div>
                <div class="form-group"><label for="longitude">Longitude</label><input id="longitude" name="longitude" placeholder="-46.6333" value="{{ $field('longitude') }}"></div>
            </div>
        </div>

        <div class="form-section">
            <h3>Fotos do imóvel</h3>
            <label class="photo-drop" for="photos">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/></svg>
                <strong>Clique para selecionar fotos</strong>
                <small>JPG, PNG ou WEBP · até 4 MB cada</small>
                <input id="photos" name="photos[]" type="file" accept="image/*" multiple style="display: none;">
            </label>
            <p id="selected-files" style="font-size: 13px; color: var(--muted); margin-top: 10px;"></p>
            <div class="photo-url-row">
                <input id="photo-url" name="photo_url" type="url" placeholder="Ou cole a URL de uma foto (https://...)">
                <button type="button" id="add-photo-url" class="btn btn-outline-blue">Adicionar</button>
            </div>
            <div id="dynamic-photos"></div>
            @if (count($photos) > 0)
                <div class="photo-preview" id="photo-preview">
                    @foreach ($photos as $photo)
                        <input type="hidden" name="existing_photos[]" value="{{ $photo }}">
                        <div class="photo-preview-item">
                            <img src="{{ $photo }}" alt="Prévia do imóvel">
                            <label title="Remover foto" style="position: absolute; top: 5px; right: 5px; width: 24px; height: 24px; border-radius: 50%; background: rgba(3,20,87,.75); color: white; display: grid; place-items: center; cursor: pointer;">
                                <input type="checkbox" name="remove_photos[]" value="{{ $photo }}" {{ in_array($photo, $removedPhotos, true) ? 'checked' : '' }} style="position: absolute; opacity: 0; width: 24px; height: 24px; cursor: pointer;">
                                <span aria-hidden="true">×</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="photo-preview" id="photo-preview"></div>
            @endif
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--line); padding-top: 22px; margin-top: 10px;">
            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-blue">Cancelar</a>
            <button type="submit" class="btn btn-blue">{{ $isEdit ? 'Salvar alterações' : 'Salvar imóvel' }}</button>
        </div>
    </div>
</form>

<script>
    (function () {
        var fileInput = document.getElementById('photos');
        var fileInfo = document.getElementById('selected-files');
        if (fileInput && fileInfo) {
            fileInput.addEventListener('change', function () {
                var names = Array.prototype.map.call(fileInput.files || [], function (file) { return file.name; });
                fileInfo.textContent = names.length
                    ? names.length + ' arquivo(s) selecionado(s) para envio: ' + names.join(', ')
                    : '';
            });
        }

        var urlInput = document.getElementById('photo-url');
        var addButton = document.getElementById('add-photo-url');
        var dynamic = document.getElementById('dynamic-photos');
        var preview = document.getElementById('photo-preview');
        if (urlInput && addButton && dynamic && preview) {
            addButton.addEventListener('click', function () {
                var url = urlInput.value.trim();
                if (!url) {
                    return;
                }
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'photo_urls[]';
                hidden.value = url;
                dynamic.appendChild(hidden);

                var item = document.createElement('div');
                item.className = 'photo-preview-item';
                var image = document.createElement('img');
                image.src = url;
                image.alt = 'Prévia do imóvel';
                var remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '×';
                remove.setAttribute('aria-label', 'Remover foto');
                remove.addEventListener('click', function () {
                    dynamic.removeChild(hidden);
                    preview.removeChild(item);
                });
                item.appendChild(image);
                item.appendChild(remove);
                preview.appendChild(item);
                urlInput.value = '';
            });
        }
    })();
</script>
@endsection
