<?php

namespace toubilib\infra\repositories\interface;

use toubilib\api\dtos\CredentialsDTO;
use toubilib\core\domain\entities\user\User;

interface AuthnRepositoryInterface {
    public function getUser(string $email) : User;
    public function saveUser(CredentialsDTO $credential, ?int $role = 1): void;
}