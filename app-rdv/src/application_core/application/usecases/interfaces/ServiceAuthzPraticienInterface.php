<?php

namespace rdvs\core\application\usecases\interfaces;


interface ServiceAuthzPraticienInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id) : bool;
}