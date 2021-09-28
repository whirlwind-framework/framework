<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class ValidatorCollection
{
    protected $validators = [];

    protected $messages = [];

    public function __construct(ValidatorInterface ...$validators)
    {
        $this->validators = $validators;
    }

    public function addValidator(ValidatorInterface $validator) : self
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function validate($value, array $context = []) : bool
    {
        $valid = true;
        $this->messages = [];
        foreach ($this->validators as $validator) {
            if (!$validator->validate($value, $context)) {
                $valid = false;
                $this->messages[] = $validator->getMessage();
            }
        }
        return $valid;
    }

    public function getMessages() : array
    {
        return $this->messages;
    }
}
