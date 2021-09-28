<?php declare(strict_types=1);

namespace Whirlwind\Domain\Dto;

abstract class Dto implements DtoInterface
{
    public function __construct(array $data)
    {
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if (isset($data[$propertyName])) {
                $this->$propertyName = $data[$propertyName];
            }
        }
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
