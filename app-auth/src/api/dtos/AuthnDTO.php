<?php

namespace auth\api\dtos;


use Exception;

class AuthnDTO {
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $role,
    ) {}
}