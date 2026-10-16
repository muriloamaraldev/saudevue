<?php

namespace App\Messaging;

use App\Contracts\PublishesEvents;

/**
 * Publicação desligada (RABBITMQ_ENABLED=false ou ambiente de teste).
 *
 * Mantém a assinatura igual à do publisher real, então o controller não
 * precisa de condicional nenhuma.
 */
class NullEventPublisher implements PublishesEvents
{
    /** @var array<int, array{routing_key: string, payload: array<string, mixed>}> */
    public array $publicados = [];

    public function publish(string $routingKey, array $payload): void
    {
        $this->publicados[] = [
            'routing_key' => $routingKey,
            'payload' => $payload,
        ];
    }
}
