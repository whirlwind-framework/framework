<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Exception;

use Throwable;

class InsertException extends \Exception
{
    protected $data;

    public function __construct(array $data, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }
}
