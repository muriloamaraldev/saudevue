<?php

namespace App\Events;

use App\Models\Patient;

/**
 * Fato: os dados de um paciente foram atualizados.
 */
final class PatientUpdated
{
    public const ROUTING_KEY = 'patient.updated';

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
