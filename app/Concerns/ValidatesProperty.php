<?php

namespace App\Concerns;

use Closure;
use Illuminate\Validation\Rule;

trait ValidatesProperty
{
    /**
     * Validation rules shared by the HTTP requests and the Livewire property form.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function propertyRules(bool $creating = true): array
    {
        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return [
            'title' => [...$required, 'string', 'max:255'],
            'purpose' => [...$required, Rule::in(['venda', 'aluguel'])],
            'type' => [...$required, 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['disponivel', 'reservado', 'vendido'])],
            'price' => [...$required, 'numeric', 'min:0'],
            'condo_fee' => ['nullable', 'numeric', 'min:0'],
            'iptu' => ['nullable', 'numeric', 'min:0'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'lot_area' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'suite' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'parking' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'string', 'max:4000'],
            'description' => ['nullable', 'string'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:150'],
            'state' => ['nullable', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'featured' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'photo_url' => ['nullable', 'string', 'max:2048', $this->photoLocationRule()],
            'photo_urls' => ['nullable', 'array'],
            'photo_urls.*' => ['string', 'max:2048', $this->photoLocationRule()],
            'existing_photos' => ['nullable', 'array'],
            'existing_photos.*' => ['string', 'max:2048', $this->photoLocationRule()],
            'remove_photos' => ['nullable', 'array'],
            'remove_photos.*' => ['string', 'max:2048', $this->photoLocationRule()],
            'removed_photos' => ['nullable', 'array'],
            'removed_photos.*' => ['string', 'max:2048', $this->photoLocationRule()],
        ];
    }

    /**
     * Validation rule for a single photo URL.
     */
    protected function photoLocationRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_string($value) || ! self::isValidPhotoLocation($value)) {
                $fail('O campo :attribute deve ser uma URL http(s) ou um caminho local /uploads/....');
            }
        };
    }

    /**
     * Only HTTP(S) URLs and local /uploads/... paths are valid photo locations.
     */
    protected static function isValidPhotoLocation(string $value): bool
    {
        $value = trim($value);

        if (str_starts_with($value, '/uploads/')) {
            return ! str_contains($value, '..')
                && ! str_contains($value, '\\')
                && basename($value) !== '';
        }

        return in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true)
            && filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}
