<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Strategy;

use DateTimeImmutable;

class DateTimeImmutableStrategy implements StrategyInterface
{
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param $value
     * @param array|null $data
     * @param null $oldValue
     *
     * @return DateTimeImmutable|false|null
     */
    public function hydrate($value, ?array $data = null, $oldValue = null)
    {
        if (null === $value) {
            return null;
        }

        return DateTimeImmutable::createFromFormat(static::DATETIME_FORMAT, $value);
    }

    /**
     * @param $value
     * @param object|null $object
     *
     * @return string|null
     */
    public function extract($value, ?object $object = null)
    {
        if (!($value instanceof DateTimeImmutable)) {
            return null;
        }

        return $value->format(static::DATETIME_FORMAT);
    }
}
