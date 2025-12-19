<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

readonly class GetCmsEntityListParam implements PaginatorParamInterface
{
    public function __construct(
        #[MethodParam(description: '文章目录')]
        public string|int|null $catalogId = null,
        #[MethodParam(description: '模型代号')]
        public ?string $modelCode = null,
        #[MethodParam(description: '搜索关键词')]
        public string $keyword = '',
        #[MethodParam(description: '每页条数')]
        public int $pageSize = 10,
        #[MethodParam(description: '当前页数')]
        public int $currentPage = 1,
        #[MethodParam(description: '上一次拉取时，最后一条数据的主键ID')]
        public ?int $lastId = null,
    ) {
    }
}
