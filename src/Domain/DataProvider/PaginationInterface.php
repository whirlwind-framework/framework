<?php declare(strict_types=1);

namespace Whirlwind\Domain\DataProvider;

interface PaginationInterface
{
    public function getTotal(): int;

    public function getPage(): int;

    public function getPageSize(): int;

    public function getNumberOfPages(): int;
}
