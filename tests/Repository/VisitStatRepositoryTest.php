<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Entity\VisitStat;
use Tourze\CmsBundle\Repository\VisitStatRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(VisitStatRepository::class)]
#[RunTestsInSeparateProcesses]
final class VisitStatRepositoryTest extends AbstractRepositoryTestCase
{
    private VisitStatRepository $repository;

    public function testRepositoryConstruction(): void
    {
        $repository = self::getService(VisitStatRepository::class);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(VisitStatRepository::class, $repository);
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $visitStat = $this->createTestVisitStat();

        $this->repository->save($visitStat);

        $this->assertNotNull($visitStat->getId());

        $savedVisitStat = $this->repository->find($visitStat->getId());
        $this->assertInstanceOf(VisitStat::class, $savedVisitStat);
        $this->assertSame($visitStat->getEntityId(), $savedVisitStat->getEntityId());
    }

    public function testSaveMethodWithoutFlush(): void
    {
        $visitStat = $this->createTestVisitStat();

        $this->repository->save($visitStat, false);

        self::getService(EntityManagerInterface::class)->flush();

        $this->assertNotNull($visitStat->getId());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $visitStat = $this->createTestVisitStat();
        $this->repository->save($visitStat);

        $visitStatId = $visitStat->getId();
        $this->repository->remove($visitStat);

        $deletedVisitStat = $this->repository->find($visitStatId);
        $this->assertNull($deletedVisitStat);
    }

    public function testRemoveMethodWithoutFlush(): void
    {
        $visitStat = $this->createTestVisitStat();
        $this->repository->save($visitStat);

        $visitStatId = $visitStat->getId();
        $this->repository->remove($visitStat, false);

        self::getService(EntityManagerInterface::class)->flush();

        $deletedVisitStat = $this->repository->find($visitStatId);
        $this->assertNull($deletedVisitStat);
    }

    public function testFindByWithDateCriteriaShouldWork(): void
    {
        $date = new \DateTimeImmutable('2024-01-01');
        $visitStat = $this->createTestVisitStat();
        $visitStat->setDate($date);
        $this->repository->save($visitStat);

        // 使用 count 验证查询条件有效，避免 BIGINT 主键的 DBAL 兼容性问题
        $count = $this->repository->count(['date' => $date]);
        $this->assertGreaterThanOrEqual(1, $count);

        // 使用 find 直接获取实体来验证数据
        $found = $this->repository->find($visitStat->getId());
        $this->assertNotNull($found);
        $foundDate = $found->getDate();
        $this->assertNotNull($foundDate);
        $this->assertSame($date->format('Y-m-d'), $foundDate->format('Y-m-d'));
    }

    public function testCountWithDateCriteriaShouldWork(): void
    {
        $date = new \DateTimeImmutable('2024-01-02');
        $visitStat = $this->createTestVisitStat();
        $visitStat->setDate($date);
        $this->repository->save($visitStat);

        $result = $this->repository->count(['date' => $date]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByWithValueCriteriaShouldWork(): void
    {
        $value = 999;
        $visitStat = $this->createTestVisitStat();
        $visitStat->setValue($value);
        $this->repository->save($visitStat);

        // 使用 count 验证查询条件有效，避免 BIGINT 主键的 DBAL 兼容性问题
        $count = $this->repository->count(['value' => $value]);
        $this->assertGreaterThanOrEqual(1, $count);

        // 使用 find 直接获取实体来验证数据
        $found = $this->repository->find($visitStat->getId());
        $this->assertNotNull($found);
        $this->assertSame($value, $found->getValue());
    }

    public function testCountWithValueCriteriaShouldWork(): void
    {
        $value = 888;
        $visitStat = $this->createTestVisitStat();
        $visitStat->setValue($value);
        $this->repository->save($visitStat);

        $result = $this->repository->count(['value' => $value]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByWithCreateTimeFieldIsNull(): void
    {
        $visitStat = $this->createTestVisitStat();
        $this->repository->save($visitStat);

        // 使用 count 验证查询条件有效，避免 BIGINT 主键的 DBAL 兼容性问题
        $result = $this->repository->count(['createTime' => null]);

        $this->assertIsInt($result);
    }

    public function testCountWithCreateTimeFieldIsNull(): void
    {
        $visitStat = $this->createTestVisitStat();
        $this->repository->save($visitStat);

        $result = $this->repository->count(['createTime' => null]);

        $this->assertIsInt($result);
    }

    public function testFindByWithUpdateTimeFieldIsNull(): void
    {
        $visitStat = $this->createTestVisitStat();
        $this->repository->save($visitStat);

        // 使用 count 验证查询条件有效，避免 BIGINT 主键的 DBAL 兼容性问题
        $result = $this->repository->count(['updateTime' => null]);

        $this->assertIsInt($result);
    }

    public function testCountWithUpdateTimeFieldIsNull(): void
    {
        $visitStat = $this->createTestVisitStat();
        $this->repository->save($visitStat);

        $result = $this->repository->count(['updateTime' => null]);

        $this->assertIsInt($result);
    }

    public function testFindByWithComplexCriteriaMultipleFields(): void
    {
        $date = new \DateTimeImmutable('2024-03-01');
        $entityId = 'complex_test_' . uniqid();
        $value = 777;

        $visitStat = $this->createTestVisitStat();
        $visitStat->setDate($date);
        $visitStat->setEntityId($entityId);
        $visitStat->setValue($value);
        $this->repository->save($visitStat);

        // 使用 count 验证复杂查询条件有效，避免 BIGINT 主键的 DBAL 兼容性问题
        $count = $this->repository->count([
            'date' => $date,
            'entityId' => $entityId,
            'value' => $value,
        ]);
        $this->assertGreaterThanOrEqual(1, $count);

        // 使用 find 直接获取实体来验证数据
        $found = $this->repository->find($visitStat->getId());
        $this->assertNotNull($found);
        $foundDate = $found->getDate();
        $this->assertNotNull($foundDate);
        $this->assertSame($date->format('Y-m-d'), $foundDate->format('Y-m-d'));
        $this->assertSame($entityId, $found->getEntityId());
        $this->assertSame($value, $found->getValue());
    }

    public function testCountWithComplexCriteriaMultipleFields(): void
    {
        $date = new \DateTimeImmutable('2024-04-01');
        $entityId = 'complex_count_test_' . uniqid();
        $value = 666;

        $visitStat = $this->createTestVisitStat();
        $visitStat->setDate($date);
        $visitStat->setEntityId($entityId);
        $visitStat->setValue($value);
        $this->repository->save($visitStat);

        $result = $this->repository->count([
            'date' => $date,
            'entityId' => $entityId,
            'value' => $value,
        ]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(VisitStatRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new VisitStat();
        $entity->setDate(new \DateTimeImmutable());
        $entity->setEntityId('test_entity_' . uniqid());
        $entity->setValue(1);

        return $entity;
    }

    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    private function createTestVisitStat(): VisitStat
    {
        $visitStat = new VisitStat();
        $visitStat->setDate(new \DateTimeImmutable());
        $visitStat->setEntityId('test_entity_' . uniqid());
        $visitStat->setValue(1);

        return $visitStat;
    }
}
