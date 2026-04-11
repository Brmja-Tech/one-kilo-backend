<?php

namespace App\Services\Payments\Kashier;

use RuntimeException;
use Throwable;

class KashierException extends RuntimeException
{
    public function __construct(
        string $message,
        protected array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function context(): array
    {
        return $this->context;
    }
}

