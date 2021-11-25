<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

interface ValidatorInterface
{
    public function validate($value, array $context = []): bool;

    public function getMessage(): string;
}
