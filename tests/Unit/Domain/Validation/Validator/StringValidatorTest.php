<?php

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\StringValidator;
use PHPUnit\Framework\TestCase;

class StringValidatorTest extends TestCase
{
    protected $validator;

    protected $min = 2;

    protected $max = 5;

    protected function setUp() : void
    {
        $this->validator = new StringValidator(['min' => $this->min, 'max' => $this->max]);
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate('12'));
        $this->assertTrue($this->validator->validate('12345'));
        $this->assertFalse($this->validator->validate('1'));
        $this->assertFalse($this->validator->validate('123456'));
    }

    public function testSetInvalidMin()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid min param value');
        $this->validator->setMin('-5');
    }

    public function testSetInvalidMax()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid max param value');
        $this->validator->setMax('-5');
    }

    public function testSetInvalidLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid length param value');
        $this->validator->setLength('-5');
    }

    public function testSetEncoding()
    {
        $this->assertSame($this->validator, $this->validator->setEncoding('UTF8'));
    }
}
