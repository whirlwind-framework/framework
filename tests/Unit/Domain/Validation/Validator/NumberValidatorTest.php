<?php

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\NumberValidator;
use PHPUnit\Framework\TestCase;

class NumberValidatorTest extends TestCase
{
    protected $validator;

    protected $min = 1;

    protected $max = 1000;

    protected function setUp() : void
    {
        $this->validator = new NumberValidator(['min' => $this->min, 'max' => $this->max]);
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate(1));
        $this->assertTrue($this->validator->validate('1'));
        $this->assertTrue($this->validator->validate(1.23));
        $this->assertTrue($this->validator->validate('1.23'));
        $this->assertFalse($this->validator->validate('str'));
        $this->assertFalse($this->validator->validate(-1));
        $this->assertFalse($this->validator->validate(2000));
    }
}
