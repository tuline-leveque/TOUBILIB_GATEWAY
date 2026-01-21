<?php

namespace auth\infra\repositories\interface;


use auth\api\dtos\CredentialsDTO;
use auth\core\domain\entities\user\User;

interface AuthnRepositoryInterface {
    public function getUser(string $email) : User;
    public function saveUser(CredentialsDTO $credential, ?int $role = 1): void;
}