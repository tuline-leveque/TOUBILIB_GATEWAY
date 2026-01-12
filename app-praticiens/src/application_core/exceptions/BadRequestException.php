<?php

namespace praticiens\core\exceptions;

use Exception;

class BadRequestException extends Exception
{

    public function __construct(string $message, ?Exception $previous = null)
    {
        //400 unauthorized
        parent::__construct($message, 400, $previous);
    }
}