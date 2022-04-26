<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Collection;

use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Collection\Collection;

class CollectionTest extends TestCase
{
    /** @var  Collection */
    protected $collection;

    protected $items;

    public function setUp(): void
    {
        $this->items = [
            new DummyEntity(1),
            new DummyEntity(2)
        ];
        $this->collection = new Collection(DummyEntity::class, $this->items);
    }

    public function testCount()
    {
        $this->assertEquals(\count($this->items), count($this->collection));
    }

    public function testIterator()
    {
        $i = 0;
        foreach ($this->collection as $item) {
            $i++;
            $this->assertInstanceOf(DummyEntity::class, $item);
        }
        $this->assertEquals(\count($this->items), $i);
    }

    public function testValidateAdd()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->collection->add(new \stdClass());
    }

    public function testAdd()
    {
        $count = \count($this->collection);
        $this->collection->add(new DummyEntity(3));
        $this->assertEquals(($count + 1), \count($this->collection));
    }

    public function testClear()
    {
        $this->collection->add(new DummyEntity(1));
        $this->collection->add(new DummyEntity(2));
        $this->collection->clear();
        $this->assertEquals(0, $this->collection->count());
    }

    public function testArrayAccess()
    {
        $key = 4;
        $oldCount = $this->collection->count();
        $item = new DummyEntity(3);
        $this->collection[$key] = $item;
        $this->assertEquals($oldCount + 1, $this->collection->count());
        $this->assertTrue(isset($this->collection[$key]));
        $this->assertEquals($item, $this->collection[$key]);
        unset($this->collection[$key]);
        $this->assertEquals($oldCount, $this->collection->count());
    }
}

class DummyEntity
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
