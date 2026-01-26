<?php

namespace rdvs\core\application\usecases\interfaces;


interface ServiceAuthzPatientInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id) : bool;
}