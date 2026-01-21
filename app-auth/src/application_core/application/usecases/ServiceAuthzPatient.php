<?php

namespace auth\core\application\usecases;


use auth\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use auth\core\domain\entities\user\User;

class ServiceAuthzPatient implements ServiceAuthzPatientInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id): bool {
        return ($role == User::PATIENT && $user_id === $ressource_id);
    }
}