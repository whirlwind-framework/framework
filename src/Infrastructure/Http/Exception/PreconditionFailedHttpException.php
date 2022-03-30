<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class PreconditionFailedHttpException extends HttpException
{
    public function __construct(string $message = 'Precondition Failed', $code = 0, \Exception $previous = null)
    {
        parent::__construct(412, $message, $code, $previous);
    }
}
