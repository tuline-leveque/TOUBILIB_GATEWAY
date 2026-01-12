<?php

namespace toubilib\core\exceptions;

use Exception;

class ConnexionException extends Exception
{

    public function __construct(string $message, ?Exception $previous = null)
    {
        //401 unauthorized
        parent::__construct($message, 401, $previous);
    }
}