<?php

namespace Tests\Feature;

use App\Contracts\PublishesEvents;
use App\Messaging\AmqpEventPublisher;
use App\Messaging\NullEventPublisher;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    public function test_patients_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/patients');

        $response->assertOk();
    }

    public function test_patient_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/patients', [
            'name' => 'Maria da Silva',
            'cpf' => '529.982.247-25',
            'birth_date' => '1990-05-20',
            'phone' => '11999998888',
            'email' => 'maria@example.com',
            'notes' => 'Apenas penicilina.',
        ]);

        $response->assertSessionHasNoErrors();

        // O CPF é gravado normalizado, sem máscara.
        $this->assertDatabaseHas('patients', [
            'user_id' => $user->id,
            'name' => 'Maria da Silva',
            'cpf' => '52998224725',
        ]);
    }

    public function test_invalid_cpf_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/patients', [
            'name' => 'Inválido',
            'cpf' => '111.111.111-11',
        ]);

        $response->assertSessionHasErrors('cpf');
        $this->assertDatabaseCount('patients', 0);
    }

    public function test_cpf_may_repeat_across_different_tenants(): void
    {
        $donoA = User::factory()->create();
        $donoB = User::factory()->create();

        Patient::factory()->create(['user_id' => $donoA->id, 'cpf' => '52998224725']);

        $response = $this->actingAs($donoB)->post('/patients', [
            'name' => 'Outro profissional',
            'cpf' => '529.982.247-25',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame(2, Patient::withoutTrashed()->count());
    }

    public function test_cpf_must_be_unique_within_a_tenant(): void
    {
        $user = User::factory()->create();

        Patient::factory()->create(['user_id' => $user->id, 'cpf' => '52998224725']);

        $response = $this->actingAs($user)->post('/patients', [
            'name' => 'Duplicado',
            'cpf' => '529.982.247-25',
        ]);

        $response->assertSessionHasErrors('cpf');
    }

    public function test_patient_can_be_updated(): void
    {
        $user = User::factory()->create();
        $paciente = Patient::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/patients/{$paciente->id}", [
            'name' => 'Nome Atualizado',
            'cpf' => $paciente->cpf,
            'birth_date' => $paciente->birth_date,
            'phone' => '11888887777',
            'email' => 'atualizado@example.com',
            'notes' => 'Atualizado.',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Nome Atualizado', $paciente->refresh()->name);
    }

    public function test_patient_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $paciente = Patient::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/patients/{$paciente->id}");

        $response->assertRedirect(route('patients.index'));
        $this->assertSoftDeleted('patients', ['id' => $paciente->id]);
    }

    public function test_user_cannot_view_another_users_patient(): void
    {
        $dono = User::factory()->create();
        $invasor = User::factory()->create();

        $paciente = Patient::factory()->create(['user_id' => $dono->id]);

        $this->actingAs($invasor)
            ->get("/patients/{$paciente->id}")
            ->assertForbidden();

        $this->actingAs($invasor)
            ->get('/patients')
            ->assertOk()
            ->assertDontSee($paciente->name);
    }

    public function test_patient_registration_publishes_an_event(): void
    {
        $publisher = new NullEventPublisher;
        $this->app->instance(PublishesEvents::class, $publisher);

        $user = User::factory()->create();

        $this->actingAs($user)->post('/patients', [
            'name' => 'Maria da Silva',
            'cpf' => '529.982.247-25',
        ])->assertSessionHasNoErrors();

        $this->assertCount(1, $publisher->publicados);

        $evento = $publisher->publicados[0];

        $this->assertSame('patient.registered', $evento['routing_key']);
        $this->assertSame('patient.registered', $evento['payload']['event']);
        $this->assertSame($user->id, $evento['payload']['data']['tenant_id']);
        $this->assertNotNull($evento['payload']['data']['patient_id']);
    }

    public function test_patient_update_publishes_an_event(): void
    {
        $publisher = new NullEventPublisher;
        $this->app->instance(PublishesEvents::class, $publisher);

        $user = User::factory()->create();
        $paciente = Patient::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put("/patients/{$paciente->id}", [
            'name' => 'Nome Alterado',
            'cpf' => $paciente->cpf,
            'birth_date' => $paciente->birth_date,
        ])->assertSessionHasNoErrors();

        $this->assertCount(1, $publisher->publicados);
        $this->assertSame('patient.updated', $publisher->publicados[0]['routing_key']);
        $this->assertSame($paciente->id, $publisher->publicados[0]['payload']['data']['patient_id']);
    }

    public function test_patient_is_saved_even_when_the_broker_is_down(): void
    {
        // Aponta para uma porta fechada: a conexão é recusada na hora.
        config(['rabbitmq.port' => 1]);
        $this->app->instance(PublishesEvents::class, new AmqpEventPublisher);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/patients', [
            'name' => 'Sobrevivente',
            'cpf' => '529.982.247-25',
        ]);

        // A requisição não pode falhar por causa do broker: o registro já
        // está no banco quando a publicação é tentada.
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('patients', [
            'user_id' => $user->id,
            'name' => 'Sobrevivente',
        ]);
    }
}
