<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class UnprocessableEntityHttpException extends HttpException
{
    public function __construct(string $message = 'Unprocessable entity.', $code = 0, \Exception $previous = null)
    {
        parent::__construct(422, $message, $code, $previous);
    }
}
