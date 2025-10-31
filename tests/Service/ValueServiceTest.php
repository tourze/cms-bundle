<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Service;

use CmsBundle\Service\ValueService;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ValueService::class)]
#[RunTestsInSeparateProcesses]
final class ValueServiceTest extends AbstractIntegrationTestCase
{
    public function testServiceCreation(): void
    {
        $valueService = self::getService(ValueService::class);
        $this->assertInstanceOf(ValueService::class, $valueService);
    }

    public function testCreateQueryBuilderInternallyUsed(): void
    {
        $valueService = self::getService(ValueService::class);

        // 通过buildSearchSubQuery间接测试createQueryBuilder的功能
        $searchableAttributes = [1, 2, 3];
        $keyword = 'test';
        $queryBuilder = $valueService->buildSearchSubQuery($searchableAttributes, $keyword);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
        $this->assertSame('v', $queryBuilder->getRootAliases()[0]);
    }

    public function testBuildSearchSubQuery(): void
    {
        $valueService = self::getService(ValueService::class);
        $searchableAttributes = [1, 2, 3];
        $keyword = 'test';

        $queryBuilder = $valueService->buildSearchSubQuery($searchableAttributes, $keyword);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
        $this->assertSame('v', $queryBuilder->getRootAliases()[0]);
    }

    protected function onSetUp(): void
    {
        // 必要的设置
    }
}
