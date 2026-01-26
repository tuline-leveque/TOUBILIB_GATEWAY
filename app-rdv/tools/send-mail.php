<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connexion au service "rabbitmq" défini dans le docker-compose
$connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'pass');
$channel = $connection->channel();

// Déclaration de la file
$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage('Test de message depuis api.rdvs !');
$channel->basic_publish($msg, '', 'hello');

echo " [x] Message envoyé à RabbitMQ\n";

$channel->close();
$connection->close();