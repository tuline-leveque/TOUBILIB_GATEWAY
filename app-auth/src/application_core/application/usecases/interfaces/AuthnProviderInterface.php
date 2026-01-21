<?php

namespace auth\core\application\usecases\interfaces;

use auth\api\dtos\AuthnDTO;
use auth\api\dtos\InputAuthnDTO;

interface AuthnProviderInterface {
    public function signin(InputAuthnDTO $user_dto): AuthnDTO;
}