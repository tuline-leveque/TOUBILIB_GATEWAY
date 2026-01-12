<?php

namespace toubilib\core\exceptions;

use Exception;

class CreneauException extends Exception
{

    public function __construct(string $message, ?Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}