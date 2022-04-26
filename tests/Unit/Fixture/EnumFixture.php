<?php

declare(strict_types=1);

namespace Test\Unit\Fixture;

use Whirlwind\Domain\Enum;

class EnumFixture extends Enum
{
    protected array $values;

    public function __construct(string $value, array $values)
    {
        $this->values = $values;
        parent::__construct($value);
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
