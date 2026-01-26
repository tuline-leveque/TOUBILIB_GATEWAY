<?php

namespace auth\core\application\usecases;


use auth\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use auth\core\domain\entities\user\User;

class ServiceAuthzPraticien implements ServiceAuthzPraticienInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id): bool
    {
        return ($role == User::PRATICIEN && $user_id === $ressource_id);
    }
}