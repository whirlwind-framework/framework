<?php

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\RequiredValidator;
use PHPUnit\Framework\TestCase;

class RequiredValidatorTest extends TestCase
{
    protected $validator;

    protected $constructorErrorMessage = 'Test message';

    protected function setUp() : void
    {
        $this->validator = new RequiredValidator(['message' => $this->constructorErrorMessage]);
    }

    public function testSetMessage()
    {
        $this->assertEquals($this->constructorErrorMessage, $this->validator->getMessage());
        $message = 'Custom message';
        $this->validator->setMessage($message);
        $this->assertEquals($message, $this->validator->getMessage());
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate(1));
        $this->assertTrue($this->validator->validate('str'));
        $this->assertTrue($this->validator->validate('0'));
        $this->assertTrue($this->validator->validate(0));
        $this->assertTrue($this->validator->validate(false));
        $this->assertFalse($this->validator->validate(''));
        $this->assertFalse($this->validator->validate([]));
        $this->assertFalse($this->validator->validate(null));
    }
}
