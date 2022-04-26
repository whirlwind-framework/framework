<?php

namespace Test\Unit\Domain\Validation\Factory;

use Whirlwind\Domain\Validation\Validator\ValidatorCollection;
use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Validation\Factory\ValidatorCollectionFactory;

class ValidatorCollectionFactoryTest extends TestCase
{
    protected $factory;

    protected function setUp() : void
    {
        $this->factory = new ValidatorCollectionFactory();
    }

    public function testCreateInvalidParams()
    {
        $this->expectException(\TypeError::class);
        $this->factory->create('params');
    }

    public function testCreate()
    {
        $collection = $this->factory->create();
        $this->assertInstanceOf(ValidatorCollection::class, $collection);
    }
}
