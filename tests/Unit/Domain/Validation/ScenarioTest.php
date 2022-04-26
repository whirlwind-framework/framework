<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Validation;

use Whirlwind\Domain\Validation\Exception\ValidateException;
use Whirlwind\Domain\Validation\Scenario;
use Whirlwind\Domain\Validation\Factory\ValidatorCollectionFactory;
use Whirlwind\Domain\Validation\Factory\ValidatorFactory;
use Whirlwind\Domain\Validation\Validator\IntegerValidator;
use Whirlwind\Domain\Validation\Validator\RequiredValidator;
use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Dto\Dto;

class ScenarioTest extends TestCase
{
    protected $scenario;

    protected function setUp() : void
    {
        $this->scenario = new Scenario(new ValidatorFactory(), new ValidatorCollectionFactory());
    }

    public function testValidateDto()
    {
        $params = [
            'user_id' => 1,
            'firstName' => 'John',
        ];
        $dto = $this->getMockBuilder(Dto::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toArray'])
            ->getMock();
        $dto
            ->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue($params));
        $this->scenario->addValidator('user_id', new IntegerValidator());
        $this->scenario->addValidator('user_id', new RequiredValidator());
        $this->scenario->addValidator('firstName', new RequiredValidator());
        $this->scenario->addValidator('firstName', new RequiredValidator([
            'when' => static fn ($params) => $params['user_id'] === 1
        ]));

        $this->assertTrue($this->scenario->validateDto($dto));
    }

    public function testValidateRequestException()
    {
        $this->expectException(ValidateException::class);
        $params = [
            'user_id' => 1,
        ];
        $dto = $this->getMockBuilder(Dto::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toArray'])
            ->getMock();
        $dto
            ->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue($params));
        $this->scenario->addValidator('user_id', new IntegerValidator());
        $this->scenario->addValidator('user_id', new RequiredValidator());
        $this->scenario->addValidator('firstName', new RequiredValidator());
        $this->assertTrue($this->scenario->validateDto($dto));
    }
}
