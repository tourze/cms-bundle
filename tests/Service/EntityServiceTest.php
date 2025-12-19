<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Service;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Repository\EntityRepository;
use Tourze\CmsBundle\Service\EntityService;
use Tourze\CmsBundle\Service\ModelService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(EntityService::class)]
#[RunTestsInSeparateProcesses]
final class EntityServiceTest extends AbstractIntegrationTestCase
{
    public function testServiceCreation(): void
    {
        $entityService = self::getService(EntityService::class);
        $this->assertInstanceOf(EntityService::class, $entityService);
    }

    public function testFindEntityById(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create test entity
        $entity = new Entity();
        $entity->setTitle('Test Entity');
        $entity->setState(EntityState::PUBLISHED);  // 设置必需的 state 字段
        $entityManager->persist($entity);
        $entityManager->flush();

        $entityService = self::getService(EntityService::class);
        $result = $entityService->findEntityById((int) $entity->getId());

        $this->assertNotNull($result);
        $this->assertSame((int) $entity->getId(), $result->getId());
    }

    public function testFindEntityByIdReturnsNull(): void
    {
        $entityService = self::getService(EntityService::class);
        $result = $entityService->findEntityById(999);

        $this->assertNull($result);
    }

    public function testFindEntityBy(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create test entity
        $entity = new Entity();
        $entity->setTitle('Test Entity');
        $entity->setState(EntityState::PUBLISHED);  // 设置必需的 state 字段
        $entityManager->persist($entity);
        $entityManager->flush();

        $entityService = self::getService(EntityService::class);
        $result = $entityService->findEntityBy(['title' => 'Test Entity']);

        $this->assertNotNull($result, 'Entity should be found');
        $this->assertSame('Test Entity', $result->getTitle());
    }

    public function testFindEntitiesBy(): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();

        // Create test entities
        $entity1 = new Entity();
        $entity1->setTitle('Test Entity 1');
        $entity1->setState(EntityState::PUBLISHED);  // 设置必需的 state 字段
        $entityManager->persist($entity1);

        $entity2 = new Entity();
        $entity2->setTitle('Test Entity 2');
        $entity2->setState(EntityState::PUBLISHED);  // 设置必需的 state 字段
        $entityManager->persist($entity2);

        $entityManager->flush();

        $entityService = self::getService(EntityService::class);
        $results = $entityService->findEntitiesBy([]);

        $this->assertGreaterThanOrEqual(2, \count($results));
    }

    public function testFindPublishedEntitiesByModel(): void
    {
        $entityService = self::getService(EntityService::class);
        $modelService = self::getService(ModelService::class);

        // 假设存在一个模型，我们可以创建一个或使用现有的
        $models = $modelService->findBy([]);
        if (0 === \count($models)) {
            self::markTestSkipped('没有可用的模型进行测试');
        }

        $model = $models[0];
        $results = $entityService->findPublishedEntitiesByModel($model, 10, 0);

        $this->assertIsArray($results);
        // 结果可能为空，这是正常的
    }

    public function testCreatePublishedEntitiesQueryBuilder(): void
    {
        $entityService = self::getService(EntityService::class);
        $queryBuilder = $entityService->createPublishedEntitiesQueryBuilder();

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
        $this->assertSame('a', $queryBuilder->getRootAliases()[0]);
    }

    public function testCreateQueryBuilder(): void
    {
        // 直接通过服务容器获取Repository以满足PHPStan规则
        // 避免通过EntityManager->getRepository()导致的静态分析警告
        $repository = self::getService(EntityRepository::class);

        $queryBuilderInstance = $repository->createQueryBuilder('testAlias');

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilderInstance);
        $this->assertSame('testAlias', $queryBuilderInstance->getRootAliases()[0]);
    }

    protected function onSetUp(): void
    {
        // 必要的设置
    }
}
