<?php

namespace toubilib\core\application\usecases;
use toubilib\api\dtos\AuthnDTO;
use toubilib\api\dtos\InputAuthnDTO;
use toubilib\core\application\usecases\interfaces\AuthnProviderInterface;
use toubilib\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use toubilib\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use toubilib\core\domain\entities\user\User;
use toubilib\core\exceptions\ConnexionException;
use toubilib\infra\repositories\interface\AuthnRepositoryInterface;

class ServiceAuthzPraticien implements ServiceAuthzPraticienInterface {
    public function isGranted(string $user_id, int $role, string $ressource_id): bool
    {
        return ($role == User::PRATICIEN && $user_id === $ressource_id);
    }
}