<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class DateTimeValidator extends AbstractValidator
{
    protected $message = 'Value has not valid date';

    /**
     * @var string
     */
    protected $format = 'Y-m-d H:i:s';
    /**
     * @var null|string
     */
    protected $before = null;
    /**
     * @var null|string
     */
    protected $after = null;

    /**
     * @param string $format
     * @return void
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setBefore($value): self
    {
        if (!$this->isValidDate($value)) {
            throw new \InvalidArgumentException(
                "Expected a valid date, got '{$value}' instead."
                . " 2016-12-08, 2016-12-02 14:58, tomorrow are considered valid dates"
            );
        }

        $this->before = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setAfter($value): self
    {
        if (!$this->isValidDate($value)) {
            throw new \InvalidArgumentException(
                "Expected a valid date, got '{$value}' instead."
                . " 2016-12-08, 2016-12-02 14:58, tomorrow are considered valid dates"
            );
        }

        $this->after = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function isValidDate($value): bool
    {
        return false !== \strtotime($value);
    }

    /**
     * @param $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty && empty($value)) {
            return true;
        }

        if (!\is_string($value)) {
            return false;
        }

        if (
            null !== $this->before
            && $this->isValidDate($value)
            && ($this->getTimestamp($this->before) <= $this->getTimestamp($value))
        ) {
            $this->message = "Value must be a date before {$this->before}.";

            return false;
        }

        if (
            null !== $this->after
            && $this->isValidDate($value)
            && ($this->getTimestamp($this->after) >= $this->getTimestamp($value))
        ) {
            $this->message = "Value must be a date after {$this->after}";

            return false;
        }

        return \date_create_from_format($this->format, $value) !== false;
    }

    /**
     * @param string $date
     *
     * @return int
     */
    private function getTimestamp(string $date): int
    {
        return \strtotime($date);
    }
}
