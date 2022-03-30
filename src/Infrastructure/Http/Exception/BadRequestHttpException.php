<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class BadRequestHttpException extends HttpException
{
    public function __construct(string $message = 'Bad request', $code = 0, \Exception $previous = null)
    {
        parent::__construct(400, $message, $code, $previous);
    }
}
