<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Accessor;

use ReflectionClass;

class MethodAccessor implements AccessorInterface
{
    public function set(object $object, ReflectionClass $reflection, $name, $value)
    {
        $setter = 'set' . \str_replace(' ', '', \ucwords(\str_replace('_', ' ', $name)));
        if (!$reflection->hasMethod($setter)) {
            return;
        }
        if (\is_callable([$object, $setter])) {
            $object->$setter($value);
        }
    }

    public function get(object $object, ReflectionClass $reflection, $name)
    {
        $getter = 'get' . \str_replace(' ', '', \ucwords(\str_replace('_', ' ', $name)));
        if (\is_callable([$object, $getter])) {
            return $object->$getter();
        }
        return null;
    }

    public function getPropertyNames(object $object, ReflectionClass $reflection)
    {
        $properties = [];
        $methods = \get_class_methods($object);
        foreach ($methods as $method) {
            if (\strpos($method, 'get') === 0 && \is_callable([$object, $method])) {
                $properties[] = \lcfirst(\substr($method, 3));
            }
        }
        return $properties;
    }
}
