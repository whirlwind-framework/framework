<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Hydrator\Stub;

class StubEntity
{
    private $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
