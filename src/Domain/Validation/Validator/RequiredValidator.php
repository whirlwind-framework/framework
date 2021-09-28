<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class RequiredValidator extends AbstractValidator
{
    protected $message = 'Value is required';

    public function validate($value, array $context = []): bool
    {
        if (empty($value)) {
            return false;
        }
        return true;
    }
}
