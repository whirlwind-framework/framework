<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Exception;

class UnprocessableEntityHttpException extends HttpException
{
    /**
     * @var array
     */
    protected array $errorCollection;

    /**
     * @param array $errorCollection
     * @param string $message
     * @param $code
     * @param \Exception|null $previous
     */
    public function __construct(
        array $errorCollection = [],
        string $message = 'Unprocessable entity.',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct(422, $message, $code, $previous);
        $this->errorCollection = $errorCollection;
    }

    /**
     * @return array
     */
    public function getErrorCollection(): array
    {
        return $this->errorCollection;
    }
}
