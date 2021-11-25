<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class IntegerValidator extends NumberValidator
{
    protected $pattern = '/^\s*[+-]?\d+\s*$/';

    protected $message = 'Value must be an integer';
}
