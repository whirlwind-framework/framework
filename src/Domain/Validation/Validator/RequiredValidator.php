<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class RequiredValidator extends AbstractValidator
{
    protected $message = 'Value is required';

    public function validate($value, array $context = []): bool
    {
        return !($value === '' || $value === [] || $value === null);
    }
}
