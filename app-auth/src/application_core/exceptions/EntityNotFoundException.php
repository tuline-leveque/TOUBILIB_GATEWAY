<?php


namespace auth\core\exceptions;

use Exception;

class EntityNotFoundException extends Exception {
    private $entity;
    public function __construct(string $message, string $entity, ?Exception $previous = null)
    {
        parent::__construct($message, 404, $previous);
        $this->entity = $entity;
    }
    public function getEntity(): string {
        return $this->entity;
    }
}