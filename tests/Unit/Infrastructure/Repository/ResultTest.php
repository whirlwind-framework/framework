<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Repository;

use DG\BypassFinals;
use Whirlwind\Infrastructure\Repository\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    private array $items = [
        [
            'id' => 'test',
        ],
    ];
    private Result $result;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->result = new Result($this->items);
    }

    public function testValid()
    {
        self::assertTrue($this->result->valid());
        $this->result->next();
        self::assertFalse($this->result->valid());
    }

    public function testCurrent()
    {
        $expected = $this->items[0];
        self::assertEquals($expected, $this->result->current());
    }

    public function testRewind()
    {
        $this->result->next();
        self::assertFalse($this->result->valid());
        $this->result->rewind();
        self::assertTrue($this->result->valid());
    }

    public function testOffsetExists()
    {
        self::assertTrue($this->result->offsetExists(0));
        self::assertFalse($this->result->offsetExists('test'));
    }

    public function testOffsetUnset()
    {
        $this->result->offsetUnset(0);
        self::assertFalse($this->result->offsetExists(0));
    }

    public function testKey()
    {
        self::assertEquals(0, $this->result->key());
    }

    public function testOffsetGet()
    {
        $expected = $this->items[0];
        self::assertEquals($expected, $this->result->offsetGet(0));
        self::assertNull($this->result->offsetGet('test'));
    }

    public function testOffsetSet()
    {
        $expected = [
            'id' => 'new'
        ];
        $this->result[] = $expected;
        self::assertSame($expected, $this->result[1]);
    }

    public function testNext()
    {
        $expected = [
            'id' => 'new'
        ];
        $this->result[] = $expected;

        self::assertSame($expected, $this->result->next());
    }
}
