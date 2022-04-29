<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

use Whirlwind\Domain\Dto\DtoInterface;
use Whirlwind\Domain\Dto\FluentDto;
use Whirlwind\Domain\Validation\Exception\ValidateException;
use Whirlwind\Domain\Validation\Scenario;

class ScenarioValidator extends AbstractValidator
{
    /**
     * @var Scenario
     */
    protected $scenario;

    /**
     * ScenarioValidator constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!$this->scenario) {
            throw new \InvalidArgumentException('Scenario parameter is required');
        }
    }

    /**
     * @param Scenario $scenario
     */
    public function setScenario(Scenario $scenario): void
    {
        $this->scenario = $scenario;
    }

    /**
     * @param $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty && (null === $value || [] === $value)) {
            return true;
        }

        try {
            return $this->applyScenarioByValueType($value);
        } catch (ValidateException $e) {
            $attribute = \key($e->getErrorCollection());
            $this->message = \sprintf('Invalid attribute \'%s\'. %s', $attribute, $e->getFirstError());

            return false;
        }
    }

    /**
     * @return bool
     * @throws ValidateException
     */
    protected function applyScenarioByValueType($value): bool
    {
        if (!\is_array($value) && !($value instanceof DtoInterface)) {
            return false;
        }

        if ($value instanceof FluentDto) {
            return $this->scenario->validateFluentDto($value);
        } elseif ($value instanceof DtoInterface) {
            return $this->scenario->validateDto($value);
        }

        return $this->scenario->validateArray($value);
    }
}
