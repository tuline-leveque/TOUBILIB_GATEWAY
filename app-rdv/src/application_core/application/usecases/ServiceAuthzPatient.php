<?php

namespace toubilib\core\application\usecases;
use toubilib\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use toubilib\core\domain\entities\user\User;

class ServiceAuthzPatient implements ServiceAuthzPatientInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id): bool {
        return ($role == User::PATIENT && $user_id === $ressource_id);
    }
}