<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Helper;

use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * 测试辅助类，用于生成 PaginationInterface 的测试实现.
 */
class PaginationTestHelper
{
    /**
     * 创建一个简单的 PaginationInterface Mock 对象
     *
     * @param array<int, mixed> $items        分页数据项
     * @param int               $totalCount   总数量
     * @param int               $currentPage  当前页码
     * @param int               $itemsPerPage 每页项目数
     *
     * @return PaginationInterface<int, mixed>
     */
    public static function createPaginationMock(
        array $items = [],
        int $totalCount = 0,
        int $currentPage = 1,
        int $itemsPerPage = 10,
    ): PaginationInterface {
        return new SimplePaginationImplementation($items, $totalCount, $currentPage, $itemsPerPage);
    }
}
