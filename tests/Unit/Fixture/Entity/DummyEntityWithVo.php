<?php

declare(strict_types=1);

namespace Test\Unit\Fixture\Entity;

class VoFixture
{
    protected $voVal;

    public function __construct(string $voVal)
    {
        $this->voVal = $voVal;
    }

    public function getVoVal(): string
    {
        return $this->voVal;
    }
}

class DummyEntityWithVo
{
    protected $property;

    protected $vo;

    public function __construct($property, VoFixture $vo)
    {
        $this->property = $property;
        $this->vo = $vo;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function getVo(): VoFixture
    {
        return $this->vo;
    }
}
