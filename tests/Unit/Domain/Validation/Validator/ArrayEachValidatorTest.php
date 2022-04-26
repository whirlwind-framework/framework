<?php

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\ArrayEachValidator;
use Whirlwind\Domain\Validation\Validator\IntegerValidator;
use PHPUnit\Framework\TestCase;

class ArrayEachValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp() : void
    {
        $this->validator = new ArrayEachValidator(['validator' => new IntegerValidator()]);
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate([1, 2]));
        $this->assertFalse($this->validator->validate([1, 's']));
    }
}
