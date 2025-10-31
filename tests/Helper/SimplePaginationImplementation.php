<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Helper;

use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * 简单的 PaginationInterface 实现，用于测试.
 *
 * @implements PaginationInterface<int, mixed>
 * @implements \IteratorAggregate<int, mixed>
 */
class SimplePaginationImplementation implements PaginationInterface, \IteratorAggregate
{
    /** @var array<int, mixed> */
    private array $items;

    private int $totalCount;

    private int $currentPage;

    private int $itemsPerPage;

    /**
     * @param array<int, mixed> $items
     */
    public function __construct(array $items, int $totalCount, int $currentPage, int $itemsPerPage)
    {
        $this->items = $items;
        $this->totalCount = 0 !== $totalCount ? $totalCount : \count($items);
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getItems(): iterable
    {
        return $this->items;
    }

    public function getTotalItemCount(): int
    {
        return $this->totalCount;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPage;
    }

    public function getItemNumberPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setCurrentPageNumber(int $pageNumber): void
    {
        $this->currentPage = $pageNumber;
    }

    public function setItemNumberPerPage(int $numItemsPerPage): void
    {
        $this->itemsPerPage = $numItemsPerPage;
    }

    public function setTotalItemCount(int $numTotal): void
    {
        $this->totalCount = $numTotal;
    }

    public function setItems(iterable $items): void
    {
        if (\is_array($items)) {
            /* @var array<int, mixed> $items */
            $this->items = $items;
        } else {
            /** @var array<int, mixed> $converted */
            $converted = iterator_to_array($items);
            $this->items = $converted;
        }
    }

    public function setPaginatorOptions(array $options): void
    {
    }

    public function getPaginatorOption(string $name): mixed
    {
        return null;
    }

    public function setCustomParameters(array $parameters): void
    {
    }

    public function getCustomParameter(string $name): mixed
    {
        return null;
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function offsetExists(mixed $offset): bool
    {
        if (!\is_int($offset)) {
            return false;
        }

        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!\is_int($offset)) {
            return null;
        }

        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            $this->items[] = $value;
        } elseif (\is_int($offset)) {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (\is_int($offset)) {
            unset($this->items[$offset]);
        }
    }
}
