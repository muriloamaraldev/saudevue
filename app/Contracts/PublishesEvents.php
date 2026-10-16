<?php

namespace App\Contracts;

/**
 * Porta de saída dos eventos de domínio.
 *
 * O controller depende da interface, não do AMQP — assim os testes rodam
 * sem broker e trocar o transporte (RabbitMQ, SQS, log) não toca em regra
 * de negócio.
 */
interface PublishesEvents
{
    /**
     * Publica um evento no exchange configurado.
     *
     * Nunca deve lançar exceção para o chamador: falha de publicação não
     * pode derrubar o cadastro que já foi salvo.
     *
     * @param  array<string, mixed>  $payload
     */
    public function publish(string $routingKey, array $payload): void;
}
