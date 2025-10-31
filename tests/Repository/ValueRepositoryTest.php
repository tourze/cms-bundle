<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Repository;

use CmsBundle\Entity\Value;
use CmsBundle\Repository\ValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ValueRepository::class)]
#[RunTestsInSeparateProcesses]
final class ValueRepositoryTest extends AbstractRepositoryTestCase
{
    private ValueRepository $repository;

    public function testSaveMethodShouldPersistEntity(): void
    {
        $value = $this->createTestValue();
        $value->setData('test_save_method');

        $this->repository->save($value);

        $this->assertNotNull($value->getId());

        $savedValue = $this->repository->find($value->getId());
        $this->assertInstanceOf(Value::class, $savedValue);
        $this->assertSame($value->getData(), $savedValue->getData());
    }

    public function testSaveMethodWithoutFlush(): void
    {
        $value = $this->createTestValue();
        $value->setData('test_save_no_flush');

        $this->repository->save($value, false);

        self::getService(EntityManagerInterface::class)->flush();

        $this->assertNotNull($value->getId());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $value = $this->createTestValue();
        $value->setData('test_remove_method');
        $this->repository->save($value);

        $valueId = $value->getId();
        $this->repository->remove($value);

        $deletedValue = $this->repository->find($valueId);
        $this->assertNull($deletedValue);
    }

    public function testRemoveMethodWithoutFlush(): void
    {
        $value = $this->createTestValue();
        $value->setData('test_remove_no_flush');
        $this->repository->save($value);

        $valueId = $value->getId();
        $this->repository->remove($value, false);

        self::getService(EntityManagerInterface::class)->flush();

        $deletedValue = $this->repository->find($valueId);
        $this->assertNull($deletedValue);
    }

    public function testFindByWithDataFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setData(null);
        $this->repository->save($value);

        $result = $this->repository->findBy(['data' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testCountWithDataFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setData(null);
        $this->repository->save($value);

        $result = $this->repository->count(['data' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByWithModelFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setModel(null);
        $this->repository->save($value);

        $result = $this->repository->findBy(['model' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testCountWithModelFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setModel(null);
        $this->repository->save($value);

        $result = $this->repository->count(['model' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByWithAttributeFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setAttribute(null);
        $this->repository->save($value);

        $result = $this->repository->findBy(['attribute' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testCountWithAttributeFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setAttribute(null);
        $this->repository->save($value);

        $result = $this->repository->count(['attribute' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByWithEntityFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setEntity(null);
        $this->repository->save($value);

        $result = $this->repository->findBy(['entity' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testCountWithEntityFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $value->setEntity(null);
        $this->repository->save($value);

        $result = $this->repository->count(['entity' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByWithCreateTimeFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $this->repository->save($value);

        $result = $this->repository->findBy(['createTime' => null]);

        $this->assertIsArray($result);
    }

    public function testCountWithCreateTimeFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $this->repository->save($value);

        $result = $this->repository->count(['createTime' => null]);

        $this->assertIsInt($result);
    }

    public function testFindByWithUpdateTimeFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $this->repository->save($value);

        $result = $this->repository->findBy(['updateTime' => null]);

        $this->assertIsArray($result);
    }

    public function testCountWithUpdateTimeFieldIsNull(): void
    {
        $value = $this->createTestValue();
        $this->repository->save($value);

        $result = $this->repository->count(['updateTime' => null]);

        $this->assertIsInt($result);
    }

    public function testFindByWithAttributeAssociation(): void
    {
        $value = $this->createTestValue();
        $value->setAttribute(null);
        $this->repository->save($value);

        $result = $this->repository->findBy(['attribute' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testCountWithAttributeAssociation(): void
    {
        $value = $this->createTestValue();
        $value->setAttribute(null);
        $this->repository->save($value);

        $result = $this->repository->count(['attribute' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByWithEntityAssociation(): void
    {
        $value = $this->createTestValue();
        $value->setEntity(null);
        $this->repository->save($value);

        $result = $this->repository->findBy(['entity' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, \count($result));
    }

    public function testCountWithEntityAssociation(): void
    {
        $value = $this->createTestValue();
        $value->setEntity(null);
        $this->repository->save($value);

        $result = $this->repository->count(['entity' => null]);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindByDataAsNullShouldReturnAllMatchingEntitiesNew(): void
    {
        $value1 = $this->createTestValue();
        $value1->setData(null);
        $this->repository->save($value1);

        $value2 = $this->createTestValue();
        $value2->setData(null);
        $this->repository->save($value2);

        $result = $this->repository->findBy(['data' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, \count($result));
        foreach ($result as $value) {
            $this->assertNull($value->getData());
        }
    }

    public function testCountByDataAsNullShouldReturnCorrectNumberNew(): void
    {
        $initialCount = $this->repository->count(['data' => null]);

        $value1 = $this->createTestValue();
        $value1->setData(null);
        $this->repository->save($value1);

        $value2 = $this->createTestValue();
        $value2->setData(null);
        $this->repository->save($value2);

        $finalCount = $this->repository->count(['data' => null]);

        $this->assertIsInt($finalCount);
        $this->assertSame($initialCount + 2, $finalCount);
    }

    public function testFindOneByAssociationAttributeShouldReturnMatchingEntity(): void
    {
        $value = $this->createTestValue();
        $value->setAttribute(null);
        $this->repository->save($value);

        $result = $this->repository->findOneBy(['attribute' => null]);

        $this->assertInstanceOf(Value::class, $result);
        $this->assertNull($result->getAttribute());
    }

    public function testCountByAssociationAttributeShouldReturnCorrectNumber(): void
    {
        $initialCount = $this->repository->count(['attribute' => null]);

        $value1 = $this->createTestValue();
        $value1->setAttribute(null);
        $this->repository->save($value1);

        $value2 = $this->createTestValue();
        $value2->setAttribute(null);
        $this->repository->save($value2);

        $finalCount = $this->repository->count(['attribute' => null]);

        $this->assertIsInt($finalCount);
        $this->assertSame($initialCount + 2, $finalCount);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ValueRepository::class);
    }

    protected function getRepository(): ValueRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $value = new Value();
        $value->setRawData(['test' => 'data']);
        $value->setData('test_value_'.uniqid());

        return $value;
    }

    private function createTestValue(): Value
    {
        $value = new Value();
        $value->setRawData(['test' => 'data']);
        $value->setData('test_data_'.uniqid());

        return $value;
    }
}
