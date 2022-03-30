<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class LengthRequiredHttpException extends HttpException
{
    public function __construct(string $message = 'Length Required', $code = 0, \Exception $previous = null)
    {
        parent::__construct(411, $message, $code, $previous);
    }
}
