<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class InValidator extends AbstractValidator
{
    protected $validSet = [];

    protected $notIn = false;

    protected $message = 'Value do not belongs to valid set';

    public function setValidSet($value): self
    {
        if (!\is_array($value)
            and !($value instanceof \Closure)
            and !($value instanceof \Traversable)
        ) {
            throw new \InvalidArgumentException(
                'Invalid validSet property value. Value must be either array or \Closure or \Traversable'
            );
        }
        $this->validSet = $value;
        return $this;
    }

    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty and ($value === null || $value === [] || $value === '')) {
            return true;
        }
        if ($this->validSet instanceof \Closure) {
            $this->validSet = $this->validSet();
            if (!\is_array($this->validSet) and !($this->validSet instanceof \Traversable)) {
                throw new \InvalidArgumentException(
                    'Invalid validSet callback.'
                );
            }
        }
        $in = false;
        if ($this->validSet instanceof \Traversable) {
            foreach ($this->validSet as $v) {
                if ($value == $v) {
                    $in = true;
                }
            }
        }
        if (\is_array($this->validSet)) {
            $in = \in_array($value, $this->validSet);
        }
        return $this->notIn !== $in ? true : false;
    }
}
