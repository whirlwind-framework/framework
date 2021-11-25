<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class StringValidator extends AbstractValidator
{
    protected $min = null;

    protected $max = null;

    protected $length = null;

    protected $encoding = 'UTF-8';

    protected $message = 'Value must be a string';

    public function setMin($min): self
    {
        if (\intval($min) <= 0) {
            throw new \InvalidArgumentException('Invalid min param value');
        }
        $this->min = (int)$min;
        return $this;
    }

    public function setMax($max): self
    {
        if (\intval($max) <= 0) {
            throw new \InvalidArgumentException('Invalid max param value');
        }
        $this->max = (int)$max;
        return $this;
    }

    public function setLength($length): self
    {
        if (\intval($length) <= 0) {
            throw new \InvalidArgumentException('Invalid length param value');
        }
        $this->length = (int)$length;
        return $this;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty && $this->isEmptyValue($value)) {
            return true;
        }
        if (!\is_string($value)) {
            return false;
        }
        $length = \mb_strlen($value, $this->encoding);
        if ($this->length !== null && $this->length != $length) {
            $this->message = 'Invalid string length';
            return false;
        }
        if ($this->min !== null && $this->min > $length) {
            $this->message = 'String length is too short';
            return false;
        }
        if ($this->max !== null && $this->max < $length) {
            $this->message = 'String length is too long';
            return false;
        }
        return true;
    }

    protected function isEmptyValue($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (\is_string($value) && \trim($value) === '') {
            return true;
        }

        return false;
    }
}
