<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\UuidValidator;
use PHPUnit\Framework\TestCase;

class UuidValidatorTest extends TestCase
{
    /**
     * @var UuidValidator
     */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new UuidValidator();
    }

    /**
     * @param $value
     *
     * @dataProvider emptyValuesDataProvider
     */
    public function testValidatorSkippedEmptyValues($value)
    {
        $this->assertTrue($this->validator->validate($value));
    }

    public function emptyValuesDataProvider(): array
    {
        return [
            [
                'value' => null,
            ],
            [
                'value' => '',
            ],
            [
                'value' => '    ',
            ],
        ];
    }

    public function testValidationSuccessful()
    {
        $this->assertTrue($this->validator->validate('455a87cb-0d91-45a2-ab65-77ca7337c565'));
    }

    public function testFailedValidation()
    {
        $this->assertFalse($this->validator->validate('invalid'));
    }

    public function testValidateNil()
    {
        $this->assertTrue($this->validator->validate(UuidValidator::NIL));
    }

    public function testGetMessage()
    {
        $expected = 'Value has not valid UUID';

        $this->assertEquals($expected, $this->validator->getMessage());
    }
}
