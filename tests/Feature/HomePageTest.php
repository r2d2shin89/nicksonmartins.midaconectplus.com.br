<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_available_properties(): void
    {
        Property::factory()->create([
            'title' => 'Apartamento premium com vista panorâmica',
            'purpose' => 'venda',
            'type' => 'Apartamento',
            'city' => 'São Paulo',
            'status' => 'disponivel',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Apartamento premium com vista panorâmica')
            ->assertSee('Portfólio exclusivo');
    }

    public function test_home_filters_properties_by_purpose_and_city(): void
    {
        Property::factory()->create([
            'title' => 'Casa exclusiva em Alphaville',
            'purpose' => 'venda',
            'city' => 'Barueri',
            'neighborhood' => 'Alphaville',
        ]);
        Property::factory()->create([
            'title' => 'Studio mobiliado na Consolação',
            'purpose' => 'aluguel',
            'city' => 'São Paulo',
            'neighborhood' => 'Consolação',
        ]);

        $this->get(route('home', ['purpose' => 'aluguel', 'city' => 'São Paulo']))
            ->assertOk()
            ->assertSee('Studio mobiliado na Consolação')
            ->assertDontSee('Casa exclusiva em Alphaville');
    }

    public function test_home_hides_sold_properties(): void
    {
        Property::factory()->create([
            'title' => 'Imóvel já vendido',
            'status' => 'vendido',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Imóvel já vendido');
    }
}
