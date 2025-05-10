<?php

namespace CmsBundle\Tests\Integration;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Traversable;

/**
 * 模拟分页结果
 */
class MockPagination implements PaginationInterface, \IteratorAggregate, \Countable
{
    /**
     * 分页数据
     */
    private array $items;
    
    /**
     * 当前页码
     */
    private int $page;
    
    /**
     * 每页条数
     */
    private int $limit;
    
    /**
     * 总条数
     */
    private int $totalCount;
    
    /**
     * 构造函数
     */
    public function __construct($items, int $page = 1, int $limit = 10)
    {
        if (is_array($items)) {
            $this->items = $items;
        } elseif ($items instanceof \Traversable) {
            $this->items = iterator_to_array($items);
        } else {
            $this->items = [$items];
        }
        
        $this->page = $page;
        $this->limit = $limit;
        $this->totalCount = count($this->items);
    }
    
    /**
     * 获取全部项
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * 获取当前页码
     */
    public function getCurrentPageNumber(): int
    {
        return $this->page;
    }
    
    /**
     * 获取每页显示条数
     */
    public function getItemNumberPerPage(): int
    {
        return $this->limit;
    }
    
    /**
     * 获取总条数
     */
    public function getTotalItemCount(): int
    {
        return $this->totalCount;
    }
    
    /**
     * 设置分页参数
     */
    public function setParam(string $name, $value): self
    {
        return $this;
    }
    
    /**
     * 获取分页参数
     */
    public function getParams(): array
    {
        return [];
    }
    
    /**
     * 设置自定义参数
     */
    public function setCustomParameters(array $parameters): self
    {
        return $this;
    }
    
    /**
     * 获取自定义参数
     */
    public function getCustomParameters(): array
    {
        return [];
    }
    
    /**
     * 获取分页URL
     */
    public function getPaginationData(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'totalCount' => $this->totalCount,
        ];
    }
    
    /**
     * 实现IteratorAggregate接口
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }
    
    /**
     * 实现Countable接口
     */
    public function count(): int
    {
        return count($this->items);
    }
} 