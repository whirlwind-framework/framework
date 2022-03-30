<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class HttpException extends \Exception
{
    protected int $statusCode;

    public function __construct(int $statusCode, string $message = '', $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
