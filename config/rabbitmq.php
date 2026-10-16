<?php

return [

    /*
    |--------------------------------------------------------------------------
    | RabbitMQ
    |--------------------------------------------------------------------------
    |
    | Broker usado para publicar os eventos de domínio (patient.registered,
    | patient.updated, ...). Trocar o exchange exige alinhar com os
    | consumidores — ele é o "balcão" que todos os sistemas escutam.
    |
    */

    'host' => env('RABBITMQ_HOST', '127.0.0.1'),

    'port' => (int) env('RABBITMQ_PORT', 5672),

    'user' => env('RABBITMQ_USER', 'guest'),

    'password' => env('RABBITMQ_PASSWORD', 'guest'),

    'vhost' => env('RABBITMQ_VHOST', '/'),

    // Exchange direto: entrega a mensagem só nas filas cuja binding key
    // bate exatamente com a routing key do evento.
    'exchange' => env('RABBITMQ_EXCHANGE', 'saudevue.eventos'),

    // Permite desligar a publicação sem mudar código (útil em testes e CI).
    'enabled' => (bool) env('RABBITMQ_ENABLED', true),

];
