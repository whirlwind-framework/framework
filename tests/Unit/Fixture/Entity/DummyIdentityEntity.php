<?php

declare(strict_types=1);

namespace Test\Unit\Fixture\Entity;

use Whirlwind\Domain\Entity\IdentityInterface;

class DummyIdentityEntity implements IdentityInterface
{
    protected $id = '';

    protected $property;

    public function setProperty($property)
    {
        $this->property = $property;
    }

    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
