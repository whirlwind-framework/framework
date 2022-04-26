<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Dto;

use Whirlwind\Domain\Dto\DtoCollection;
use Whirlwind\Domain\Dto\DtoInterface;
use PHPUnit\Framework\TestCase;
use Test\Unit\Fixture\Entity\DummyEntity;

class DtoCollectionTest extends TestCase
{
    /**
     * @var DtoCollection
     */
    protected $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collection = new DtoCollection();
    }

    public function testAddIncorrectEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->collection->add($this->createMock(DummyEntity::class));
    }

    public function testAdd()
    {
        $dto = $this->createMock(DtoInterface::class);
        $this->collection->add($dto);

        $this->assertCount(1, $this->collection);
        $this->assertSame($dto, $this->collection[0]);
    }
}
