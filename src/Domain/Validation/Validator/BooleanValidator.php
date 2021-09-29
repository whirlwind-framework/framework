<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class BooleanValidator extends AbstractValidator
{
    protected $message = 'Value must be a boolean';

    /**
     * @param $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty && null === $value) {
            return true;
        }

        $result = \filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return \is_bool($result);
    }
}
