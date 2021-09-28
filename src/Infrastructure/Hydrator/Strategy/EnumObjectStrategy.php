<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Strategy;

use Whirlwind\Domain\Enum;

class EnumObjectStrategy implements StrategyInterface
{
    protected $enumName;

    public function __construct(string $enumName)
    {
        if (!\is_subclass_of($enumName, Enum::class)) {
            throw new \InvalidArgumentException;
        }
        $this->enumName = $enumName;
    }

    public function hydrate($value, ?array $data = null, $oldValue = null)
    {
        return new $this->enumName($value);
    }

    public function extract($value, ?object $object = null)
    {
        if (!($value instanceof $this->enumName)) {
            return null;
        }
        /** @var \Whirlwind\Domain\Enum $value */
        return $value->getValue();
    }
}
