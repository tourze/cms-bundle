<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Repository\EntityRepository;
use Tourze\CmsBundle\Service\ContentService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ContentService::class)]
#[RunTestsInSeparateProcesses]
final class ContentServiceTest extends AbstractIntegrationTestCase
{
    private ContentService $contentService;

    private EntityRepository $entityRepository;

    /**
     * 测试ContentService可以正常实例化并集成到容器中.
     */
    public function testContentServiceIntegration(): void
    {
        $this->assertInstanceOf(ContentService::class, $this->contentService);
    }

    /**
     * 测试基础搜索功能，使用真实的QueryBuilder.
     */
    public function testSearchByKeywordBasicFunctionality(): void
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('e');

        // 测试搜索方法不会抛出异常
        $this->contentService->searchByKeyword($queryBuilder, 'test');

        // 如果没有异常，测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试空关键词的处理.
     */
    public function testSearchByKeywordWithEmptyKeyword(): void
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('e');

        // 测试空关键词不会抛出异常
        $this->contentService->searchByKeyword($queryBuilder, '');

        // 如果没有异常，测试通过
        $this->assertTrue(true);
    }

    protected function onSetUp(): void
    {
        $this->contentService = self::getService(ContentService::class);
        $this->entityRepository = self::getService(EntityRepository::class);
    }
}
