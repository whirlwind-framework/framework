<?php

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\ArrayValidator;
use PHPUnit\Framework\TestCase;

class ArrayValidatorTest extends TestCase
{
    protected $validator;

    protected $size = 2;

    protected function setUp() : void
    {
        $this->validator = new ArrayValidator(['size' => $this->size]);
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate([3,6]));
        $this->assertFalse($this->validator->validate('str'));
        $this->assertFalse($this->validator->validate(-1));
        $this->assertFalse($this->validator->validate([1]));
    }
}
