<?php

namespace rdvs\core\application\usecases;

use rdvs\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use rdvs\core\domain\entities\user\User;

class ServiceAuthzPraticien implements ServiceAuthzPraticienInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id): bool
    {
        return ($role == User::PRATICIEN && $user_id === $ressource_id);
    }
}