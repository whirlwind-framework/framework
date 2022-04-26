<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Validation\Validator\RegularExpressionValidator;
use PHPUnit\Framework\TestCase;

class RegularExpressionValidatorTest extends TestCase
{
    /**
     * @var RegularExpressionValidator
     */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new RegularExpressionValidator();
    }

    public function testValidateSuccessful()
    {
        $this->validator->setPattern('/^[A-Z]+$/i');
        $this->assertTrue($this->validator->validate('AbC'));
    }

    public function testValidateFalse()
    {
        $this->validator->setPattern('/^[A-Z]+$/i');
        $this->assertFalse($this->validator->validate('A1-C'));
    }

    /**
     * @param $value
     *
     * @dataProvider emptyValueDataProvider
     */
    public function testValidationSkipped(?string $value)
    {
        $this->validator->setPattern('/^[A-Z]+$/i');
        $this->assertTrue($this->validator->validate($value));
    }

    public function emptyValueDataProvider(): array
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

    /**
     * @param string|null $value
     *
     * @dataProvider emptyValueDataProvider
     */
    public function testValidationNotSkippedOnEmptyValue(?string $value)
    {
        $this->validator->setPattern('/^[A-Z]+$/i');
        $this->validator->setSkipOnEmpty(false);
        $this->assertFalse($this->validator->validate($value));
    }

    public function testSetNotValidPattern()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid regex pattern: \'/^(A|B$/\'. Internal error');
        $this->validator->setPattern('/^(A|B$/');
    }

    public function testGetMessage()
    {
        $expected = 'Value does not match the pattern';

        $this->assertEquals($expected, $this->validator->getMessage());
    }
}
