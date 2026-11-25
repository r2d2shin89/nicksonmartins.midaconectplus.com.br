<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_persists_message_and_returns_ok(): void
    {
        $this->postJson('/api/messages', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'phone' => '(11) 99999-9999',
            'subject' => 'Quero comprar um imóvel',
            'message' => 'Procuro um apartamento com duas vagas.',
        ])
            ->assertCreated()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('messages', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'subject' => 'Quero comprar um imóvel',
        ]);
    }

    public function test_contact_form_rejects_empty_payload_with_422(): void
    {
        $this->postJson('/api/messages', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_admin_can_list_and_delete_messages(): void
    {
        $message = Message::factory()->create(['name' => 'Contato de teste']);

        $this->actingAs(User::factory()->create());

        $this->getJson('/api/messages')
            ->assertOk()
            ->assertJsonPath('messages.0.name', 'Contato de teste');

        $this->deleteJson('/api/messages/'.$message->id)
            ->assertOk();

        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }
}
