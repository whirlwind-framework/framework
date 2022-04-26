<?php

declare(strict_types=1);

namespace Test\Unit\Domain;

use DG\BypassFinals;
use Whirlwind\Domain\Enum;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\Unit\Fixture\EnumFixture;

class EnumTest extends TestCase
{
    protected $value = 'enum1';

    protected $values = [
        'enum1',
        'enum2'
    ];
    /**
     * @var Enum|MockObject
     */
    protected $enum;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->enum = new EnumFixture($this->value, $this->values);
    }

    /**
     * @return void
     */
    public function testEquals(): void
    {

        $anotherEnum = new EnumFixture($this->value, $this->values);
        $this->assertTrue($this->enum->equals($anotherEnum));

        $this->assertFalse($this->enum->equals(new EnumFixture('enum2', $this->values)));

        $anotherEnum->setValue('enum2');
        $this->assertFalse($this->enum->equals($anotherEnum));
    }

    public function testCreateEnumInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $value = 'test';
        $this->expectExceptionMessage('Invalid value: ' . $value);
        new EnumFixture($value, []);
    }

    public function testGetValue(): void
    {
        $this->assertEquals($this->value, $this->enum->getValue());
    }

    public function testToString(): void
    {
        $this->assertSame((string) $this->value, $this->enum->__toString());
    }
}
