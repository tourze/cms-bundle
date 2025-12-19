<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\CmsBundle\Param\GetCmsEntityListParam;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

/**
 * @internal
 */
#[CoversClass(GetCmsEntityListParam::class)]
final class GetCmsEntityListParamTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $param = new GetCmsEntityListParam();

        $this->assertNull($param->catalogId);
        $this->assertNull($param->modelCode);
        $this->assertSame('', $param->keyword);
        $this->assertSame(10, $param->pageSize);
        $this->assertSame(1, $param->currentPage);
        $this->assertNull($param->lastId);
    }

    public function testCustomValues(): void
    {
        $param = new GetCmsEntityListParam(
            catalogId: 5,
            modelCode: 'article',
            keyword: 'test',
            pageSize: 20,
            currentPage: 2,
            lastId: 100
        );

        $this->assertSame(5, $param->catalogId);
        $this->assertSame('article', $param->modelCode);
        $this->assertSame('test', $param->keyword);
        $this->assertSame(20, $param->pageSize);
        $this->assertSame(2, $param->currentPage);
        $this->assertSame(100, $param->lastId);
    }

    public function testImplementsPaginatorParamInterface(): void
    {
        $param = new GetCmsEntityListParam();

        $this->assertInstanceOf(PaginatorParamInterface::class, $param);
    }

    public function testCatalogIdCanBeString(): void
    {
        $param = new GetCmsEntityListParam(catalogId: 'string-id');

        $this->assertSame('string-id', $param->catalogId);
    }
}
