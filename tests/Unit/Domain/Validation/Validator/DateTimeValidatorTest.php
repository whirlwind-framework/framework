<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\DateTimeValidator;
use PHPUnit\Framework\TestCase;

class DateTimeValidatorTest extends TestCase
{
    /**
     * @var DateTimeValidator
     */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new DateTimeValidator();
    }

    public function testValidateSuccessful()
    {
        $this->assertTrue($this->validator->validate('2020-11-27 14:00:00'));
    }

    /**
     * @param $value
     *
     * @dataProvider valueDataProvider
     */
    public function testValidationFailed($value)
    {
        $this->assertFalse($this->validator->validate($value));
    }

    /**
     * @return array
     */
    public function valueDataProvider(): array
    {
        return [
            [
                'value' => [
                    'test',
                ],
            ],
            [
                'value' => 25,
            ],
            [
                'value' => '100500',
            ],
            [
                'value' => '123abc',
            ],
            [
                'value' => 'invalid'
            ],
        ];
    }

    /**
     * @param $value
     *
     * @dataProvider emptyValueDDataProvider
     */
    public function testValidatorSkippedEmptyValues($value)
    {
        $this->assertTrue($this->validator->validate($value));
    }

    public function emptyValueDDataProvider(): array
    {
        return [
            [
                'value' => null,
            ],
            [
                'value' => 0,
            ],
            [
                'value' => '',
            ],
            [
                'value' => [],
            ],
        ];
    }

    public function testGetMessage()
    {
        $expected = 'Value has not valid date';

        $this->assertEquals($expected, $this->validator->getMessage());
    }
}
