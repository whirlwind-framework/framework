<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Accessor;

use ReflectionClass;

class PropertyArrayAccessor implements AccessorInterface
{
    protected $propertyArrayName;

    public function __construct(string $propertyArrayName)
    {
        $this->propertyArrayName = $propertyArrayName;
    }

    protected function getPropertyArray(ReflectionClass $reflection) : \ReflectionProperty
    {
        $property = $reflection->getProperty($this->propertyArrayName);
        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(true);
        }
        return $property;
    }

    public function set(object $object, ReflectionClass $reflection, $name, $value)
    {
        $property = $this->getPropertyArray($reflection);
        $properties = (array)$property->getValue($object);
        $properties[$name] = $value;
        $property->setValue($object, $properties);
    }

    public function get(object $object, ReflectionClass $reflection, $name)
    {
        $property = $this->getPropertyArray($reflection);
        $properties = (array)$property->getValue($object);
        return (isset($properties[$name]) ? $properties[$name] : null);
    }

    public function getPropertyNames(object $object, ReflectionClass $reflection)
    {
        $property = $this->getPropertyArray($reflection);
        $properties = (array)$property->getValue($object);
        return \array_keys($properties);
    }
}
