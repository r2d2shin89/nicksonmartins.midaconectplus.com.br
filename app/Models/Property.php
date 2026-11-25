<?php

namespace App\Models;

use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'purpose',
        'type',
        'price',
        'condo_fee',
        'iptu',
        'area',
        'lot_area',
        'bedrooms',
        'bathrooms',
        'parking',
        'suite',
        'address',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'description',
        'features',
        'photos',
        'featured',
        'status',
        'created_at',
        'updated_at',
    ];

    /** @var list<string> */
    protected $appends = [
        'formatted_price',
        'formatted_area',
        'location_text',
        'first_photo',
        'maps_url',
        'whatsapp_url',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => 'disponivel',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'condo_fee' => 'decimal:2',
            'iptu' => 'decimal:2',
            'area' => 'decimal:2',
            'lot_area' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'features' => 'array',
            'photos' => 'array',
            'featured' => 'boolean',
        ];
    }

    public function getFormattedPriceAttribute(): string
    {
        $formattedPrice = Number::currency((float) ($this->price ?? 0), 'BRL', 'pt-BR', 0);

        return $this->purpose === 'aluguel' ? $formattedPrice.'/mês' : $formattedPrice;
    }

    public function getFormattedAreaAttribute(): string
    {
        if (blank($this->area) || (float) $this->area <= 0) {
            return '—';
        }

        return Number::format((float) $this->area, 0, 0, 'pt-BR').' m²';
    }

    public function getLocationTextAttribute(): string
    {
        return collect([$this->neighborhood, $this->city, $this->state])
            ->filter()
            ->implode(' · ');
    }

    public function getFirstPhotoAttribute(): string
    {
        $photos = is_array($this->photos) ? $this->photos : [];

        return (string) (collect($photos)
            ->first(fn (mixed $photo): bool => is_string($photo) && $photo !== '') ?? '/no-image.svg');
    }

    public function getMapsUrlAttribute(): string
    {
        $query = filled($this->latitude) && filled($this->longitude)
            ? number_format((float) $this->latitude, 4, '.', '').','.number_format((float) $this->longitude, 4, '.', '')
            : ($this->location_text !== '' ? $this->location_text : (string) $this->address);

        return 'https://www.google.com/maps/search/?'.http_build_query(['api' => 1, 'query' => $query]);
    }

    public function getWhatsappUrlAttribute(): string
    {
        $whatsapp = preg_replace('/\D+/', '', (string) config('broker.whatsapp')) ?: '5511988776655';
        $text = sprintf(
            'Olá, %s! Tenho interesse no imóvel "%s" — %s.',
            (string) config('broker.name'),
            (string) $this->title,
            $this->formatted_price,
        );

        return 'https://wa.me/'.$whatsapp.'?'.http_build_query(['text' => $text]);
    }
}
