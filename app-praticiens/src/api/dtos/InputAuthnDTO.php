<?php

namespace toubilib\api\dtos;

use Exception;

class InputAuthnDTO {
    private string $email;
    private string $password;

    public function __construct(array $data) {
        $this->email = $data['email'];
        $this->password = $data['password'];
    }

    /**
     * @throws Exception
     */
    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new Exception("La propriété '$property' n'existe pas.");
    }
}
