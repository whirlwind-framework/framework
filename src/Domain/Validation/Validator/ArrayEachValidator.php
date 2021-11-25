<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class ArrayEachValidator extends ArrayValidator
{
    protected $validator;

    public function __construct(array $params = [])
    {
        parent::__construct($params);
        if (!($this->validator instanceof ValidatorInterface)) {
            throw new \InvalidArgumentException('Invalid validator param value');
        }
    }

    public function setValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty && empty($value)) {
            return true;
        }
        $valid = parent::validate($value, $context);
        if (!$valid) {
            return false;
        }
        foreach ($value as $v) {
            if ($this->skipOnEmpty && empty($v)) {
                continue;
            }
            $valid = $this->validator->validate($v, $context);
            if (false === $valid) {
                $this->message = $this->validator->getMessage();
                return false;
            }
        }
        return true;
    }
}
