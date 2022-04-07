<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Repository;

interface RepositoryInterface
{
    public function findOne(array $conditions = [], array $with = [], array $select = []): object;

    public function findAll(
        array $conditions = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $with = [],
        array $select = []
    ): array;

    public function aggregate($column, $operator, array $conditions);

    public function aggregateCount(string $field = '', array $conditions = []);

    public function aggregateSum(string $field, array $conditions);

    public function aggregateAverage(string $field, array $conditions);

    public function aggregateMin(string $field, array $conditions);

    public function aggregateMax(string $field, array $conditions);

    public function insert(object $model);

    public function update(object $model);

    public function delete(object $model);

    public function updateAll(array $data, array $conditions): int;

    public function deleteAll(array $conditions): int;

    public function populate(object $entity, array $data): void;
}
