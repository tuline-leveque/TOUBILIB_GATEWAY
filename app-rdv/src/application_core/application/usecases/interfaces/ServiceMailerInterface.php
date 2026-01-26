<?php

namespace rdvs\core\application\usecases\interfaces;

interface ServiceMailerInterface
{
    public function send(string $message, string $email, string $role, string $event);
}