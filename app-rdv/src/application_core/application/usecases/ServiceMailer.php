<?php

namespace rdvs\core\application\usecases;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use rdvs\core\application\usecases\interfaces\ServiceMailerInterface;

class ServiceMailer implements ServiceMailerInterface
{

    public function send(string $message, string $email, string $role, string $event)
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'pass');
        $channel = $connection->channel();

        // On s'assure que la file 'mailer' existe
        $channel->queue_declare('mailer', false, false, false, false);

        // On prépare un tableau avec toutes les infos
        $data = [
            'event' => $event,
            'recipient' => [
                'email' => $email,
                'role' => $role
            ],
            'payload' => [
                'description' => $message,
                'timestamp' => date('c')
            ]
        ];

        // On convertit le tableau en chaîne JSON
        $payload = json_encode($data);

        $msg = new AMQPMessage($payload, [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        // On publie dans la file 'mailer'
        $channel->basic_publish($msg, '', 'mailer');

        $channel->close();
        $connection->close();
    }
}