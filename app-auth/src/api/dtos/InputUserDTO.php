<?php
namespace auth\api\dtos;

class InputUserDTO{
    private string $email;
    private string $password;
    public function __construct(array $data){
        $this->email = $data['email'];
        $this->password = $data['password'];
    }

    public function __get(string $name){
        if(property_exists($this,$name)) {
            return $this->$name;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }

    public function __set(string $name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
            return;
        }
        throw new \Exception("Propriété '$name' inexistante dans " . __CLASS__);
    }
}