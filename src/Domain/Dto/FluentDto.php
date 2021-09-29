<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Dto;

abstract class FluentDto implements DtoInterface
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * FluentDto constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($this->fields as $field) {
            if (\array_key_exists($field, $data)) {
                $this->data[$field] = $data[$field];
            }
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        foreach ($this->data as $key => $val) {
            $accessor = $this->resolveAccessor($key);
            if (\method_exists($this, $accessor)) {
                $val = $this->$accessor();
            }

            if ($val instanceof ArrayableInterface) {
                $val = $val->toArray();
            }

            $data[$key] = $val;
        }
        return $data;
    }

    /**
     * @param $key
     * @return string
     */
    protected function resolveAccessor($key): string
    {
        if (\strpos($key, 'is') !== false && \method_exists($this, $key)) {
            return $key;
        }

        return 'get' . \ucfirst($key);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool
    {
        return \array_key_exists($field, $this->data);
    }
}
