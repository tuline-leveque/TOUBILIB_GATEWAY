<?php

namespace toubilib\core\application\usecases\interfaces;

use toubilib\api\dtos\AuthnDTO;
use toubilib\api\dtos\CredentialsDTO;
use toubilib\api\dtos\InputAuthnDTO;

interface AuthnProviderInterface {
    public function signin(InputAuthnDTO $user_dto): AuthnDTO;
}