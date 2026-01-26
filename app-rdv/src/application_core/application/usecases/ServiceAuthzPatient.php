<?php

namespace rdvs\core\application\usecases;
use rdvs\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use rdvs\core\domain\entities\user\User;

class ServiceAuthzPatient implements ServiceAuthzPatientInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id): bool {
        return ($role == User::PATIENT && $user_id === $ressource_id);
    }
}