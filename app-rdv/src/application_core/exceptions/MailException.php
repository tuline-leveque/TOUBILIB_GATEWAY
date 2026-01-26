<?php

namespace rdvs\core\exceptions;

use Exception;

class MailException extends Exception
{

    public function __construct(string $message, ?Exception $previous = null)
    {
        //400 unauthorized
        parent::__construct($message, 503, $previous);
    }
}