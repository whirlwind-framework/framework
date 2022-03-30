<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class TooManyRequestsHttpException extends HttpException
{
    public function __construct(string $message = 'Too Many Requests', $code = 0, \Exception $previous = null)
    {
        parent::__construct(429, $message, $code, $previous);
    }
}
