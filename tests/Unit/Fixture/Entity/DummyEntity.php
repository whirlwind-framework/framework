<?php

declare(strict_types=1);

namespace Test\Unit\Fixture\Entity;

class DummyEntity
{
    protected $property;

    public function setProperty($property)
    {
        $this->property = $property;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
