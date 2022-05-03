<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator;

class PresenterHydrator extends Hydrator
{
    public function extract(object $object, array $fields = []): array
    {
        $reflection = $this->getReflectionClass(\get_class($object));
        $result = [];
        if ($fields === []) {
            $fields = $this->accessor->getPropertyNames($object, $reflection);
        }
        foreach ($fields as $name) {
            $result[$name] = $this->accessor->get($object, $reflection, $name);
            if (isset($this->strategies[$name])) {
                $result[$name] = $this->strategies[$name]->extract($result[$name], $object);
            } elseif (\is_iterable($result[$name])) {
                $result[$name] = $this->extractIterable($result[$name]);
            } elseif (\is_object($result[$name])) {
                $result[$name] = $this->extract($result[$name]);
            }
        }
        return $result;
    }

    public function getReflectionClass($target)
    {
        $className = \is_object($target) ? \get_class($target) : $target;
        if (!isset($this->reflectionClassMap[$className])) {
            $this->reflectionClassMap[$className] = new \ReflectionClass($className);
        }
        return $this->reflectionClassMap[$className];
    }

    private function extractIterable(iterable $items): array
    {
        $result = [];
        foreach ($items as $key => $value) {
            $result[$key] = \is_object($value) ? $this->extract($value) : $value;
        }

        return $result;
    }
}
