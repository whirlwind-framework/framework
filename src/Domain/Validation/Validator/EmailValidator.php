<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class EmailValidator extends AbstractValidator
{
    protected $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9]'
    . '(?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';

    protected $message = 'Value is not valid email';

    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty and ($value === null || \trim($value) === '')) {
            return true;
        }
        if (!\is_string($value)) {
            return false;
        }
        return (bool)\preg_match($this->pattern, $value);
    }
}
