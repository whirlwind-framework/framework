<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Factory;

use Whirlwind\Domain\Validation\Validator\ValidatorCollection;
use Whirlwind\Domain\Validation\Validator\ValidatorInterface;

class ValidatorCollectionFactory
{
    public function create(ValidatorInterface ...$validators): ValidatorCollection
    {
        return new ValidatorCollection(...$validators);
    }
}
