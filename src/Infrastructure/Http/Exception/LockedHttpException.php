<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class LockedHttpException extends HttpException
{
    public function __construct(string $message = 'Locked', $code = 0, \Exception $previous = null)
    {
        parent::__construct(423, $message, $code, $previous);
    }
}
