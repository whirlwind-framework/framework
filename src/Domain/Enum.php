<?php

declare(strict_types=1);

namespace Whirlwind\Domain;

abstract class Enum
{
    protected $value = '';

    public function __construct(string $value)
    {
        if (!\in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException("Invalid value: $value");
        }
        $this->value = $value;
    }

    abstract public function getValues(): array;

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }

    /**
     * @param Enum $value
     * @return bool
     */
    public function equals(Enum $value): bool
    {
        return $value instanceof static && $value->getValue() === $this->value;
    }
}
