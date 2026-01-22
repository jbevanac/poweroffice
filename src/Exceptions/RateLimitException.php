<?php

namespace Poweroffice\Exceptions;

use Throwable;

class RateLimitException extends PowerofficeException
{
    private int $retryAfter;

    public function __construct(string $message = "", int $retryAfter = 0, Throwable $previous = null)
    {
        parent::__construct($message, $retryAfter, $previous);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
