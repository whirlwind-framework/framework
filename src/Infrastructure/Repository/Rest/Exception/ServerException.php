<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Rest\Exception;

use Throwable;

class ServerException extends \RuntimeException
{
    protected int $httpCode;

    public function __construct(int $httpCode = 500, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
