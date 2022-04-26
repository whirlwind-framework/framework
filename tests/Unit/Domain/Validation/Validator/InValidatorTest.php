<?php

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\InValidator;
use PHPUnit\Framework\TestCase;

class InValidatorTest extends TestCase
{
    protected $validator;

    protected $validSet = [1, 3, 5, 7, 9, 11];

    protected function setUp() : void
    {
        $this->validator = new InValidator(['validSet' => $this->validSet]);
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate(1));
        $this->assertTrue($this->validator->validate(3));
        $this->assertFalse($this->validator->validate(2));
        $this->assertFalse($this->validator->validate(-1));
        $this->assertFalse($this->validator->validate(6));
    }
}
