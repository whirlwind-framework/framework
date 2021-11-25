<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Accessor;

use ReflectionClass;

interface AccessorInterface
{
    public function set(object $object, ReflectionClass $reflection, $name, $value);

    public function get(object $object, ReflectionClass $reflection, $name);

    public function getPropertyNames(object $object, ReflectionClass $reflection);
}
