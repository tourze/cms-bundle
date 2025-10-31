<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Repository;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\EntityState;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\ModelRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(EntityRepository::class)]
#[RunTestsInSeparateProcesses]
final class EntityRepositoryTest extends AbstractRepositoryTestCase
{
    public function testSaveShouldPersistEntity(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();

        $repository->save($entity);

        $this->assertNotNull($entity->getId());

        $foundEntity = $repository->find($entity->getId());
        $this->assertInstanceOf(Entity::class, $foundEntity);
        $this->assertSame($entity->getTitle(), $foundEntity->getTitle());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $repository->save($entity);
        $entityId = $entity->getId();

        $repository->remove($entity);

        $foundEntity = $repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testFindByWithModelAssociation(): void
    {
        $repository = self::getService(EntityRepository::class);
        $modelRepository = self::getService(ModelRepository::class);

        // 创建模型
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $model->setValid(true);
        $modelRepository->save($model);

        // 创建实体并关联模型
        $entity = $this->createTestEntity();
        $entity->setModel($model);
        $repository->save($entity);

        $results = $repository->findBy(['model' => $model]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertSame($model->getId(), $result->getModel()?->getId());
        }
    }

    public function testFindByWithNullModel(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setModel(null);
        $repository->save($entity);

        $results = $repository->findBy(['model' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertNull($result->getModel());
        }
    }

    public function testCountWithModelAssociation(): void
    {
        $repository = self::getService(EntityRepository::class);
        $modelRepository = self::getService(ModelRepository::class);

        // 创建模型
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $model->setValid(true);
        $modelRepository->save($model);

        // 创建实体并关联模型
        $entity = $this->createTestEntity();
        $entity->setModel($model);
        $repository->save($entity);

        $count = $repository->count(['model' => $model]);
        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithStateField(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setState(EntityState::DRAFT);
        $repository->save($entity);

        $results = $repository->findBy(['state' => EntityState::DRAFT]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertSame(EntityState::DRAFT, $result->getState());
        }
    }

    public function testCountWithStateField(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setState(EntityState::PUBLISHED);
        $repository->save($entity);

        $count = $repository->count(['state' => EntityState::PUBLISHED]);
        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithPublishTime(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $publishTime = new \DateTimeImmutable('2024-01-01 00:00:00');
        $entity->setPublishTime($publishTime);
        $repository->save($entity);

        $results = $repository->findBy(['publishTime' => $publishTime]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertSame($publishTime, $result->getPublishTime());
        }
    }

    public function testFindByWithEndTime(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $endTime = new \DateTimeImmutable('2024-12-31 23:59:59');
        $entity->setEndTime($endTime);
        $repository->save($entity);

        $results = $repository->findBy(['endTime' => $endTime]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertSame($endTime, $result->getEndTime());
        }
    }

    public function testFindByWithSortNumber(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setSortNumber(100);
        $repository->save($entity);

        $results = $repository->findBy(['sortNumber' => 100]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertSame(100, $result->getSortNumber());
        }
    }

    public function testCountWithSortNumber(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setSortNumber(50);
        $repository->save($entity);

        $count = $repository->count(['sortNumber' => 50]);
        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithNullPublishTime(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setPublishTime(null);
        $repository->save($entity);

        $results = $repository->findBy(['publishTime' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertNull($result->getPublishTime());
        }
    }

    public function testCountWithNullPublishTime(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setPublishTime(null);
        $repository->save($entity);

        $count = $repository->count(['publishTime' => null]);
        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithNullEndTime(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setEndTime(null);
        $repository->save($entity);

        $results = $repository->findBy(['endTime' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertNull($result->getEndTime());
        }
    }

    public function testCountWithNullEndTime(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setEndTime(null);
        $repository->save($entity);

        $count = $repository->count(['endTime' => null]);
        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithNullSortNumber(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setSortNumber(null);
        $repository->save($entity);

        $results = $repository->findBy(['sortNumber' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Entity::class, $result);
            $this->assertNull($result->getSortNumber());
        }
    }

    public function testCountWithNullSortNumber(): void
    {
        $repository = self::getService(EntityRepository::class);
        $entity = $this->createTestEntity();
        $entity->setSortNumber(null);
        $repository->save($entity);

        $count = $repository->count(['sortNumber' => null]);
        $this->assertGreaterThan(0, $count);
    }

    protected function onSetUp(): void
    {
    }

    /**
     * @return ServiceEntityRepository<Entity>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(EntityRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new Entity();
        $entity->setTitle('test-entity-'.uniqid());
        $entity->setState(EntityState::DRAFT);
        $entity->setSortNumber(0);

        return $entity;
    }

    private function createTestEntity(): Entity
    {
        $entity = new Entity();
        $entity->setTitle('test_entity_'.uniqid());
        $entity->setState(EntityState::DRAFT);
        $entity->setSortNumber(0);
        $entity->setRemark('Test Remark');

        return $entity;
    }
}
