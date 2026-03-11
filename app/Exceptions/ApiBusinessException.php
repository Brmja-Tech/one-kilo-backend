<?php

namespace App\Exceptions;

use Exception;

class ApiBusinessException extends Exception
{
    public function __construct(
        string $message,
        protected int $status = 422,
        protected array $data = []
    ) {
        parent::__construct($message, $status);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function data(): array
    {
        return $this->data;
    }
}
