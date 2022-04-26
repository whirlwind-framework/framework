<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Validation\Validator;

use Whirlwind\Domain\Dto\DtoInterface;
use Whirlwind\Domain\Dto\FluentDto;
use Whirlwind\Domain\Validation\Exception\ValidateException;
use Whirlwind\Domain\Validation\Scenario;
use Whirlwind\Domain\Validation\Validator\ScenarioValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ScenarioValidatorTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scenario;

    /**
     * @var ScenarioValidator
     */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scenario = $this->createMock(Scenario::class);
        $this->validator = new ScenarioValidator(['scenario' => $this->scenario]);
    }

    public function testScenarioRequiredException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Scenario parameter is required');
        new ScenarioValidator();
    }

    /**
     * @param $value
     *
     * @dataProvider emptyValueDataProvider
     */
    public function testValidateSkippedEmpty($value)
    {
        $this->assertTrue($this->validator->validate($value));
    }

    /**
     * @return array
     */
    public function emptyValueDataProvider(): array
    {
        return [
            [
                'value' => null,
            ],
            [
                'value' => [],
            ]
        ];
    }

    public function testValidateFluentDto()
    {
        $dto = $this->createMock(FluentDto::class);
        $this->scenario->expects($this->once())
            ->method('validateFluentDto')
            ->with($this->identicalTo($dto))
            ->willReturn(true);

        $this->assertTrue($this->validator->validate($dto));
    }

    public function testValidateDto()
    {
        $dto = $this->createMock(DtoInterface::class);
        $this->scenario->expects($this->once())
            ->method('validateDto')
            ->with($this->identicalTo($dto))
            ->willReturn(true);
        $this->assertTrue($this->validator->validate($dto));
    }

    public function testValidateArray()
    {
        $params = [
            'key' => 'value',
        ];

        $this->scenario->expects($this->once())
            ->method('validateArray')
            ->with($this->identicalTo($params))
            ->willReturn(true);

        $this->assertTrue($this->validator->validate($params));
    }

    public function testFailedValidationForIncorrectValueType()
    {
        $this->assertFalse($this->validator->validate(1));
    }

    public function testScenarioFailedMessage()
    {
        $params = [
            'key' => 'value',
        ];

        $this->scenario->expects($this->once())
            ->method('validateArray')
            ->with($this->identicalTo($params))
            ->willThrowException(new ValidateException(['attribute' => ['Value is required']]));

        $this->validator->validate($params);
        $expected = 'Invalid attribute \'attribute\'. Value is required';

        $this->assertEquals($expected, $this->validator->getMessage());
    }
}
