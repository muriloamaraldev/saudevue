<?php

namespace App\Messaging;

use App\Contracts\PublishesEvents;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

/**
 * Publica eventos de domínio no RabbitMQ.
 *
 * Decisões de confiabilidade aqui:
 *  - exchange direto e durável: entrega previsível, sobrevive ao restart
 *  - delivery_mode persistent: a mensagem vai pro disco do broker
 *  - publisher confirms: só damos como publicada quando o broker confirma
 *  - qualquer falha é logada e engolida — o cadário já foi salvo e não
 *    pode desabar por causa de um broker fora do ar
 */
class AmqpEventPublisher implements PublishesEvents
{
    private ?AMQPStreamConnection $connection = null;

    private ?AMQPChannel $channel = null;

    private bool $exchangeDeclarado = false;

    public function publish(string $routingKey, array $payload): void
    {
        try {
            $canal = $this->canal();

            $mensagem = new AMQPMessage($this->codificar($payload), [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'message_id' => $payload['event_id'] ?? (string) \Illuminate\Support\Str::uuid(),
                'timestamp' => time(),
                'type' => $routingKey,
            ]);

            // Confirmação do broker: garante que a mensagem foi aceita
            // antes de considerarmos a publicação concluída.
            $canal->confirm_select();
            $canal->basic_publish($mensagem, (string) config('rabbitmq.exchange'), $routingKey);
            $canal->wait_for_pending_acks(timeout: 5);

            Log::info('Evento publicado no RabbitMQ.', [
                'routing_key' => $routingKey,
                'exchange' => config('rabbitmq.exchange'),
            ]);
        } catch (Throwable $e) {
            // O dado já está no banco. Registramos e seguimos — reprocessar
            // é papel de quem consome, não de travar a requisição.
            Log::error('Falha ao publicar evento no RabbitMQ.', [
                'routing_key' => $routingKey,
                'erro' => $e->getMessage(),
            ]);

            $this->resetar();
        }
    }

    private function canal(): AMQPChannel
    {
        if ($this->channel === null || ! $this->channel->is_open()) {
            $this->conectar();
        }

        if (! $this->exchangeDeclarado) {
            $this->channel->exchange_declare(
                (string) config('rabbitmq.exchange'),
                AMQPExchangeType::DIRECT,
                passive: false,
                durable: true,
                auto_delete: false,
            );

            $this->exchangeDeclarado = true;
        }

        return $this->channel;
    }

    private function conectar(): void
    {
        $this->connection = new AMQPStreamConnection(
            (string) config('rabbitmq.host'),
            (int) config('rabbitmq.port'),
            (string) config('rabbitmq.user'),
            (string) config('rabbitmq.password'),
            (string) config('rabbitmq.vhost'),
        );

        $this->channel = $this->connection->channel();
        $this->exchangeDeclarado = false;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function codificar(array $payload): string
    {
        return (string) json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    private function resetar(): void
    {
        try {
            $this->channel?->close();
            $this->connection?->close();
        } catch (Throwable) {
            // Conexão já morreu; nada a fazer.
        }

        $this->channel = null;
        $this->connection = null;
        $this->exchangeDeclarado = false;
    }

    public function __destruct()
    {
        $this->resetar();
    }
}
