<?php declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class NumberValidator extends AbstractValidator
{
    protected $pattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';

    protected $message = 'Value must be a number';

    protected $min = null;

    protected $max = null;

    public function setMin($min) : self
    {
        if (\intval($min) <= 0) {
            throw new \InvalidArgumentException('Invalid min param value');
        }
        $this->min = (int)$min;
        return $this;
    }

    public function setMax($max) : self
    {
        if (\intval($max) <= 0) {
            throw new \InvalidArgumentException('Invalid max param value');
        }
        $this->max = (int)$max;
        return $this;
    }

    protected function normalizeNumber($value) : string
    {
        $value = (string)$value;
        $localeInfo = \localeconv();
        $decimalSeparator = isset($localeInfo['decimal_point']) ? $localeInfo['decimal_point'] : null;

        if ($decimalSeparator !== null && $decimalSeparator !== '.') {
            $value = \str_replace($decimalSeparator, '.', $value);
        }
        return $value;
    }

    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty and ($value === null || \trim((string)$value) === '')) {
            return true;
        }
        if (!\preg_match($this->pattern, $this->normalizeNumber($value))) {
            return false;
        }
        if ($this->min !== null and $this->min > $value) {
            $this->message = 'Value is too small';
            return false;
        }
        if ($this->max !== null and $this->max < $value) {
            $this->message = 'Value is too big';
            return false;
        }
        return true;
    }
}
