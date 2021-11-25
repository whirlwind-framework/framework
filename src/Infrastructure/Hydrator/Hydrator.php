<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator;

use Whirlwind\Infrastructure\Hydrator\Accessor\AccessorInterface;
use Whirlwind\Infrastructure\Hydrator\Strategy\StrategyInterface;

class Hydrator
{
    protected $strategies = [];

    protected $reflectionClassMap = [];

    protected $accessor;

    public function __construct(AccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    public function setAccessor(AccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    public function addStrategy($name, StrategyInterface $strategy): self
    {
        $this->strategies[$name] = $strategy;
        return $this;
    }

    public function hydrate($target, array $data): object
    {
        $reflection = $this->getReflectionClass($target);
        $object = \is_object($target) ? $target : $reflection->newInstanceWithoutConstructor();

        foreach ($data as $name => $value) {
            if (isset($this->strategies[$name])) {
                $oldValue = null;
                $extract = $this->extract($object, [$name]);
                if (!empty($extract[$name])) {
                    $oldValue = $extract[$name];
                }
                $value = $this->strategies[$name]->hydrate($value, $data, $oldValue);
            }
            $this->accessor->set($object, $reflection, $name, $value);
        }
        return $object;
    }

    public function extract(object $object, array $fields = []): array
    {
        $reflection = $this->getReflectionClass(\get_class($object));
        $result = [];
        if (empty($fields)) {
            $fields = $this->accessor->getPropertyNames($object, $reflection);
        }
        foreach ($fields as $name) {
            $result[$name] = $this->accessor->get($object, $reflection, $name);
            if (isset($this->strategies[$name])) {
                $result[$name] = $this->strategies[$name]->extract($result[$name], $object);
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
}
