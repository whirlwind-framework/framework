<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\DataProvider;

use Whirlwind\Domain\DataProvider\PaginationInterface;

class Pagination implements PaginationInterface, \JsonSerializable
{
    protected $total;

    protected $pageSize;

    protected $page;

    protected $numberOfPages;

    public function __construct(int $total, int $pageSize, int $page)
    {
        $this->total = $total;
        $this->pageSize = $pageSize;
        $this->page = $page;
        $this->numberOfPages = (int) (($total + $pageSize - 1) / $pageSize);
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'page' => $this->page,
            'total' => $this->total,
            'pageSize' => $this->pageSize,
            'numberOfPages' => $this->numberOfPages
        ];
    }
}
