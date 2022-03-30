<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class PreconditionRequiredHttpException extends HttpException
{
    public function __construct(string $message = 'Precondition Required', $code = 0, \Exception $previous = null)
    {
        parent::__construct(428, $message, $code, $previous);
    }
}
