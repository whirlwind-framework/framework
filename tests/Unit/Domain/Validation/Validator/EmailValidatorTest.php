<?php

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp() : void
    {
        $this->validator = new EmailValidator();
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate('john.smith@mail.com'));
        $this->assertFalse($this->validator->validate('str'));
        $this->assertFalse($this->validator->validate('str@ddd'));
    }
}
