<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class RegularExpressionValidator extends AbstractValidator
{
    /** @var string  */
    protected $message = 'Value does not match the pattern';
    /** @var string|null  */
    protected $pattern = null;

    /**
     * @param $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty and $this->isEmpty($value)) {
            return true;
        }

        if (!\is_string($value)) {
            return false;
        }

        return (bool)\preg_match($this->pattern, $value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function isEmpty($value): bool
    {
        if (null === $value) {
            return true;
        }

        if (\is_string($value) && '' === \trim($value)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        try {
            \preg_match($pattern, '');
        } catch (\Throwable $e) { // Sometimes preg_match throws exception instead of returning FALSE
        } finally {
            if (PREG_NO_ERROR !== \preg_last_error()) {
                $errorMap = [
                    PREG_NO_ERROR => 'No errors',
                    PREG_INTERNAL_ERROR => 'Internal error',
                    PREG_BACKTRACK_LIMIT_ERROR =>  'Backtrack limit error',
                    PREG_RECURSION_LIMIT_ERROR => 'Recursion limit error',
                    PREG_BAD_UTF8_ERROR => 'Bad utf8 error',
                    PREG_BAD_UTF8_OFFSET_ERROR => 'Bad utf8 offset error',
                    PREG_JIT_STACKLIMIT_ERROR => 'JIT stack limit error',
                ];

                $message = \sprintf(
                    'Invalid regex pattern: \'%s\'. %s',
                    $pattern,
                    $errorMap[\preg_last_error()] ?? ''
                );

                throw new \InvalidArgumentException($message);
            }
        }
        $this->pattern = $pattern;
    }
}
