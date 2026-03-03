<?php

namespace rdvs\core\application\usecases\interfaces;

use JsonException;
use rdvs\core\exceptions\MailException;

interface ServiceMailerInterface
{
    /**
     * @param string $message
     * @param string $email
     * @param string $role
     * @param string $event
     * @return mixed
     * @throws JsonException
     * @throws MailException
     */
    public function send(string $message, string $email, string $role, string $event);
}