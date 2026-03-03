<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Connexion à RabbitMQ
$connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'pass');
$channel = $connection->channel();
$channel->queue_declare('mailer', false, true, false, false);

echo " [*] En attente de messages dans 'mailer'. Pour sortir, Ctrl+C\n";

$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    echo " [x] Reçu : ", $data['payload']['description'], "\n";

    $mail = new PHPMailer(true);

    try {
        // 2. Configuration SMTP pour MailCatcher
        $mail->isSMTP();
        $mail->Host = 'mail.toubi'; // Nom du service dans docker-compose
        $mail->Port = 1025;         // Port SMTP de MailCatcher
        $mail->SMTPAuth = false;        // MailCatcher n'a pas besoin d'auth

        // 3. Destinataires
        $mail->setFrom('no-reply@toubilib.net', 'Toubilib');
        $mail->addAddress($data['recipient']['email']);

        // 4. Contenu
        $mail->isHTML(true);
        $mail->Subject = "Notification Toubilib : " . $data['event'];
        $mail->Body = "<h1>Mode : {$data['recipient']['role']}</h1>" .
                "<p>{$data['payload']['description']}</p>";

        $mail->send();
        echo " [OK] Email envoyé à MailCatcher\n";

        // Accuse de réception à RabbitMQ
        $msg->ack();
    } catch (Exception $e) {
        echo " [ERREUR] Message non envoyé : {$mail->ErrorInfo}\n";
    }
};

// Ne traiter qu'un message à la fois
$channel->basic_qos(null, 1, null);
$channel->basic_consume('mailer', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}