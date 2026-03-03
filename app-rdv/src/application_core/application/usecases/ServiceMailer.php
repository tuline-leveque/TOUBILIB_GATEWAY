<?php

namespace rdvs\core\application\usecases;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use rdvs\core\application\usecases\interfaces\ServiceMailerInterface;
use rdvs\core\exceptions\MailException;

class ServiceMailer implements ServiceMailerInterface
{

    /**
     * @throws \Exception
     */
    public function send(string $message, string $email, string $role, string $event)
    {
        try {
            $connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'pass');
        } catch(\Throwable $e) {
            throw new MailException("AMQPStreamConnection failed, message : ". $e->getMessage());
        }

        $channel = $connection->channel();

        // On s'assure que la file 'mailer' existe

        try {
            $channel->queue_declare('mailer', false, true, false, false);
        } catch(\Throwable $e) {
            throw new MailException("Queue declare failed, message : ". $e->getMessage());
        }

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
        try {
            $payload = json_encode($data);
        } catch(\Throwable $e) {
            throw new \JsonException("JSON Encode failed, message : ".$e->getMessage());
        }

        try {
            $msg = new AMQPMessage($payload, [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            ]);
        } catch (\Throwable $e) {
            throw new MailException("AMQPMessage failed, message: ". $e->getMessage());
        }

        // On publie dans la file 'mailer'

        try {
            $channel->basic_publish($msg, '', 'mailer');
        } catch (\Throwable $e) {
            throw new MailException("Publish failed, message: ". $e->getMessage());
        }

        $channel->close();
        $connection->close();
    }
}