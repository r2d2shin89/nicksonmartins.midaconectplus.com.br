<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_admin_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('Dashboard'));

        $this->actingAs($user)
            ->get(route('admin.properties.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.messages.index'))
            ->assertOk();
    }

    public function test_authenticated_user_can_open_property_form(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['title' => 'Imóvel para editar']);

        $this->actingAs($user)
            ->get(route('admin.properties.create'))
            ->assertOk()
            ->assertSee(__('Novo imóvel'));

        $this->actingAs($user)
            ->get(route('admin.properties.edit', $property))
            ->assertOk();
    }

    public function test_unauthenticated_api_write_returns_401(): void
    {
        $this->postJson('/api/properties', $this->propertyPayload())
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_update_and_delete_property_via_api(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/properties', $this->propertyPayload())
            ->assertCreated();

        $property = Property::where('title', 'Imóvel de teste')->firstOrFail();

        $this->actingAs($user)
            ->putJson('/api/properties/'.$property->id, $this->propertyPayload(['title' => 'Imóvel atualizado']))
            ->assertOk();

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'title' => 'Imóvel atualizado',
            'purpose' => 'venda',
        ]);

        $this->actingAs($user)
            ->deleteJson('/api/properties/'.$property->id)
            ->assertOk();

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_api_returns_property_details(): void
    {
        $property = Property::factory()->create(['title' => 'Casa com piscina']);

        $this->getJson('/api/properties/'.$property->id)
            ->assertOk()
            ->assertJsonPath('property.title', 'Casa com piscina');
    }

    public function test_livewire_property_form_creates_property(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::admin.property-form')
            ->set('title', 'Apartamento pelo Livewire')
            ->set('purpose', 'venda')
            ->set('type', 'Apartamento')
            ->set('price', 450000)
            ->set('city', 'São Paulo')
            ->call('save')
            ->assertRedirect(route('admin.properties.index'));

        $this->assertDatabaseHas('properties', [
            'title' => 'Apartamento pelo Livewire',
            'city' => 'São Paulo',
            'status' => 'disponivel',
        ]);
    }

    public function test_livewire_property_form_stores_uploaded_photo(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::admin.property-form')
            ->set('title', 'Imóvel com foto')
            ->set('purpose', 'venda')
            ->set('type', 'Apartamento')
            ->set('price', 450000)
            ->set('city', 'São Paulo')
            ->set('newPhotos', [UploadedFile::fake()->image('foto.jpg')])
            ->call('save')
            ->assertRedirect(route('admin.properties.index'));

        $property = Property::where('title', 'Imóvel com foto')->firstOrFail();
        $photo = $property->photos[0] ?? null;

        $this->assertNotNull($photo);
        $this->assertFileExists(public_path(ltrim((string) $photo, '/')));

        unlink(public_path(ltrim((string) $photo, '/')));
    }

    public function test_livewire_property_form_updates_property(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['title' => 'Imóvel antigo']);

        $this->actingAs($user);

        Livewire::test('pages::admin.property-form', ['property' => $property])
            ->assertSet('title', 'Imóvel antigo')
            ->set('title', 'Imóvel atualizado pelo Livewire')
            ->call('save')
            ->assertRedirect(route('admin.properties.index'));

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'title' => 'Imóvel atualizado pelo Livewire',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function propertyPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Imóvel de teste',
            'purpose' => 'venda',
            'type' => 'Apartamento',
            'price' => 500000,
            'condo_fee' => 700,
            'iptu' => 200,
            'area' => 90,
            'lot_area' => null,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'parking' => 1,
            'suite' => 1,
            'address' => 'Rua de teste, 100',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01000-000',
            'latitude' => '-23.5500',
            'longitude' => '-46.6300',
            'description' => 'Descrição do imóvel de teste.',
            'features' => "Piscina\nVaranda",
            'photos' => [],
            'featured' => false,
            'status' => 'disponivel',
        ], $overrides);
    }
}
