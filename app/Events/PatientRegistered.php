<?php

namespace App\Events;

use App\Models\Patient;

/**
 * Fato: um paciente foi cadastrado.
 *
 * O payload leva apenas identificadores — nada de dado clínico ou pessoal
 * trafegando pelo broker. Quem consome busca o que precisar no banco dele.
 */
final class PatientRegistered
{
    public const ROUTING_KEY = 'patient.registered';

    private function __construct(
        public readonly string $eventId,
        public readonly string $eventType,
        public readonly string $occurredAt,
        public readonly array $data,
    ) {}

    public static function fromPatient(Patient $patient): self
    {
        return new self(
            eventId: (string) \Illuminate\Support\Str::uuid(),
            eventType: self::ROUTING_KEY,
            occurredAt: now()->toIso8601String(),
            data: [
                'patient_id' => $patient->id,
                // "tenant" = dono da conta. O consumidor precisa saber de
                // qual espaço de trabalho a mensagem veio.
                'tenant_id' => $patient->user_id,
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'event_id' => $this->eventId,
            'event' => $this->eventType,
            'occurred_at' => $this->occurredAt,
            'data' => $this->data,
        ];
    }
}
