<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class ArrayValidator extends AbstractValidator
{
    protected $message = 'Value must be an array';

    protected $size = null;

    public function setSize($size)
    {
        $size = (int)$size;
        if ($size <= 0) {
            throw new \InvalidArgumentException('Invalid size param value');
        }
        $this->size = $size;
    }

    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty and ($value === [] or $value === null)) {
            return true;
        }
        if (!\is_array($value)) {
            return false;
        }
        if ($this->size !== null and $this->size !== \sizeof($value)) {
            $this->message = 'Expected array size: ' . $this->size . '. Actual: ' . \sizeof($value);
        }
        return true;
    }
}
