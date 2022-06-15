<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence;

interface QueryInterface
{
    public function all(): array;

    public function one();

    public function count($expression = '*'): int;

    public function exists(): bool;

    public function where($condition): self;

    public function andWhere($condition): self;

    public function orWhere($condition): self;

    public function filterWhere(array $condition): self;

    public function andFilterWhere(array $condition): self;

    public function orFilterWhere(array $condition): self;

    public function orderBy($columns): self;

    public function addOrderBy($columns): self;

    public function limit(int $limit): self;

    public function offset(int $offset): self;

    public function from(string $collectionName): self;
}
