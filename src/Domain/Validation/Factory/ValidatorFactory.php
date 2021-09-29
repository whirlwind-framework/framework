<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Factory;

use Whirlwind\Domain\Validation\Validator\ArrayEachValidator;
use Whirlwind\Domain\Validation\Validator\ArrayValidator;
use Whirlwind\Domain\Validation\Validator\BooleanValidator;
use Whirlwind\Domain\Validation\Validator\DateTimeValidator;
use Whirlwind\Domain\Validation\Validator\EmailValidator;
use Whirlwind\Domain\Validation\Validator\IntegerValidator;
use Whirlwind\Domain\Validation\Validator\InValidator;
use Whirlwind\Domain\Validation\Validator\NumberValidator;
use Whirlwind\Domain\Validation\Validator\RegularExpressionValidator;
use Whirlwind\Domain\Validation\Validator\RequiredValidator;
use Whirlwind\Domain\Validation\Validator\ScenarioValidator;
use Whirlwind\Domain\Validation\Validator\StringValidator;
use Whirlwind\Domain\Validation\Validator\UuidValidator;
use Whirlwind\Domain\Validation\Validator\ValidatorInterface;

class ValidatorFactory
{
    protected $validators = [
        'integer' => IntegerValidator::class,
        'required' => RequiredValidator::class,
        'array' => ArrayValidator::class,
        'arrayEach' => ArrayEachValidator::class,
        'email' => EmailValidator::class,
        'in' => InValidator::class,
        'number' => NumberValidator::class,
        'scenario' => ScenarioValidator::class,
        'boolean' => BooleanValidator::class,
        'regex' => RegularExpressionValidator::class,
        'uuid' => UuidValidator::class,
        'string' => StringValidator::class,
        'date' => DateTimeValidator::class,
    ];

    public function create(string $validatorName, array $params = []) : ValidatorInterface
    {
        if (!isset($this->validators[$validatorName])) {
            throw new \InvalidArgumentException("Validator $validatorName not supported");
        }
        return new $this->validators[$validatorName]($params);
    }
}
