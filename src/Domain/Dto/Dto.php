<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Dto;

abstract class Dto implements DtoInterface
{
    /**
     * Dto constructor.
     * @param array $data
     * @throws \ReflectionException
     */
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

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = \get_object_vars($this);
        foreach ($data as $key => $val) {
            $accessor = $this->resolveAccessor($key);
            if (\method_exists($this, $accessor)) {
                $data[$key] = $this->$accessor();
            }
            if ($data[$key] instanceof DtoInterface) {
                $data[$key] = $data[$key]->toArray();
            }
        }
        return $data;
    }

    protected function resolveAccessor($key): string
    {
        if (\strpos($key, 'is') !== false && \method_exists($this, $key)) {
            return $key;
        }

        return 'get' . \ucfirst($key);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
