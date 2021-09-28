<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation;

use Whirlwind\Domain\Validation\Validator\ValidatorInterface;
use Whirlwind\Domain\Validation\Exception\ValidateException;
use Whirlwind\Domain\Validation\Factory\ValidatorFactory;
use Whirlwind\Domain\Validation\Factory\ValidatorCollectionFactory;
use Whirlwind\Domain\Dto\DtoInterface;

class Scenario
{
    protected $validatorFactory;

    protected $validatorCollectionFactory;

    protected $attributes = [];

    public function __construct(
        ValidatorFactory $validatorFactory,
        ValidatorCollectionFactory $validatorCollectionFactory,
        array $validationRules = []
    ) {
        $this->validatorFactory = $validatorFactory;
        $this->validatorCollectionFactory = $validatorCollectionFactory;
        foreach ($validationRules as $rule) {
            if (!\is_array($rule) or \sizeof($rule) < 2) {
                throw new \InvalidArgumentException('Invalid rule configuration');
            }
            $this->addValidationRule(...$rule);
        }
    }

    public function addValidationRule($attribute, $validator, array $params = []) : self
    {
        $attributes = \is_array($attribute) ? $attribute : [$attribute];
        foreach ($attributes as $attributeName) {
            if (!($validator instanceof ValidatorInterface)) {
                $validator = $this->validatorFactory->create($validator, $params);
            }
            $this->addValidator($attributeName, $validator);
        }
        return $this;
    }

    public function addValidator($attribute, ValidatorInterface $validator) : self
    {
        if (!isset($this->attributes[$attribute])) {
            $this->attributes[$attribute] = $this->validatorCollectionFactory->create();
        }
        $this->attributes[$attribute]->addValidator($validator);
        return $this;
    }

    public function validateDto(DtoInterface $dto) : bool
    {
        $errors = [];
        $context = $dto->toArray();
        foreach ($this->attributes as $attribute => $validatorCollection) {
            $value = isset($context[$attribute]) ? $context[$attribute] : null;
            if (!$validatorCollection->validate($value, $context)) {
                foreach ($validatorCollection->getMessages() as $message) {
                    $errors[$attribute][] = $message;
                }
            }
        }
        if (\sizeof($errors) > 0) {
            throw new ValidateException($errors);
        }
        return true;
    }
}
