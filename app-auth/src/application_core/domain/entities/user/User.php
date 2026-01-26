<?php

namespace auth\core\domain\entities\user;

use Exception;

class User {
    const PRATICIEN = 10;
    const PATIENT = 1;
    private string $id;
    private string $email;
    private string $password;
    private int $role;

    public function  __construct(
        string $id,
        string $email,
        string $password,
        int $role
    ){
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
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