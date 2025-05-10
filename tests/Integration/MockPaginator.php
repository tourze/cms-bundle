<?php

namespace CmsBundle\Tests\Integration;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * 模拟分页器
 */
class MockPaginator implements PaginatorInterface
{
    /**
     * 分页
     */
    public function paginate($target, int $page = 1, ?int $limit = null, array $options = []): PaginationInterface
    {
        return new MockPagination($target, $page, $limit ?: 10);
    }
}

/**
 * 用于测试的简单分页结果实现
 */
class MockPagination implements \ArrayAccess, \Countable, \IteratorAggregate, PaginationInterface
{
    private $items;
    private int $count;
    private int $page;
    private int $limit;
    private array $customParameters = [];
    private array $paginatorOptions = [];
    private ?string $template = null;

    public function __construct($items, int $page, int $limit)
    {
        $this->items = is_array($items) ? $items : [];
        $this->count = count($this->items);
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array|\Traversable $items): void
    {
        $this->items = is_array($items) ? $items : iterator_to_array($items);
        $this->count = count($this->items);
    }

    public function getCurrentPageNumber(): int
    {
        return $this->page;
    }

    public function setCurrentPageNumber(int $pageNumber): void
    {
        $this->page = $pageNumber;
    }

    public function getItemNumberPerPage(): int
    {
        return $this->limit;
    }

    public function setItemNumberPerPage(int $numItemsPerPage): void
    {
        $this->limit = $numItemsPerPage;
    }

    public function getTotalItemCount(): int
    {
        return $this->count;
    }

    public function setTotalItemCount(int $numTotal): void
    {
        $this->count = $numTotal;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function getPaginationData(): array
    {
        return [
            'current' => $this->page,
            'numItemsPerPage' => $this->limit,
            'totalCount' => $this->count,
        ];
    }

    public function getPaginatorOptions(): array
    {
        return $this->paginatorOptions;
    }

    public function setPaginatorOptions(array $options): void
    {
        $this->paginatorOptions = $options;
    }

    public function getPaginatorOption(string $name): mixed
    {
        return $this->paginatorOptions[$name] ?? null;
    }

    public function setCustomParameters(array $parameters): void
    {
        $this->customParameters = $parameters;
    }

    public function getCustomParameter(string $name): mixed
    {
        return $this->customParameters[$name] ?? null;
    }

    public function setTemplate($template): self
    {
        $this->template = $template;
        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
} 