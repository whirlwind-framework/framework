<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Exception;

use Throwable;

class ValidateException extends \Exception
{
    protected $errorCollection = [];

    public function __construct(
        array $errorCollection = [],
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->errorCollection = $errorCollection;
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCollection() : array
    {
        return $this->errorCollection;
    }

    public function getFirstError(): string
    {
        return \current($this->errorCollection)[0];
    }
}
