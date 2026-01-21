<?php

namespace auth\core\exceptions;

use Exception;

class InternalErrorException extends Exception
{

    public function __construct(string $message, ?Exception $previous = null)
    {
        //401 unauthorized
        parent::__construct($message, 500, $previous);
    }
}