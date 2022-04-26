<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\BooleanValidator;
use PHPUnit\Framework\TestCase;

class BooleanValidatorTest extends TestCase
{
    /**
     * @var BooleanValidator
     */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new BooleanValidator();
    }

    /**
     * @param $value
     *
     * @dataProvider booleanValueDataProvider
     */
    public function testValidate($value)
    {
        $this->assertTrue($this->validator->validate($value));
    }

    public function booleanValueDataProvider(): array
    {
        return [
            [
                'value' => false,
            ],
            [
                'value' => 'false'
            ],
            [
                'value' => '0',
            ],
            [
                'value' => 0,
            ],
            [
                'value' => 'off',
            ],
            [
                'value' => 'no',
            ],
            [
                'value' => '',
            ],
            [
                'value' => '1',
            ],
            [
                'value' => 1,
            ],
            [
                'value' => 'on',
            ],
            [
                'value' => 'yes',
            ],
            [
                'value' => 'true',
            ],
        ];
    }

    public function testValidateSkipNull()
    {
        $this->assertTrue($this->validator->validate(null));
    }

    public function testValidateFalse()
    {
        $this->assertFalse($this->validator->validate(25));
    }
}
