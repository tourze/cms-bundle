<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Entity\Attribute;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Enum\FieldType;
use Tourze\CmsBundle\Repository\AttributeRepository;
use Tourze\CmsBundle\Repository\ModelRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeRepository::class)]
#[RunTestsInSeparateProcesses]
final class AttributeRepositoryTest extends AbstractRepositoryTestCase
{
    public function testSaveShouldPersistEntity(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $attribute = $this->createTestAttribute();

        $repository->save($attribute);

        $this->assertNotNull($attribute->getId());

        $foundAttribute = $repository->find($attribute->getId());
        $this->assertInstanceOf(Attribute::class, $foundAttribute);
        $this->assertSame($attribute->getName(), $foundAttribute->getName());
    }

    // remove() 方法测试
    public function testRemoveShouldDeleteEntity(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $attribute = $this->createTestAttribute();
        $repository->save($attribute);
        $attributeId = $attribute->getId();

        $repository->remove($attribute);

        $foundAttribute = $repository->find($attributeId);
        $this->assertNull($foundAttribute);
    }

    // 关联查询测试
    public function testFindByWithModelAssociation(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $modelRepository = self::getService(ModelRepository::class);

        // 创建模型
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $model->setValid(true);
        $modelRepository->save($model);

        // 创建属性并关联模型
        $attribute = $this->createTestAttribute();
        $attribute->setModel($model);
        $repository->save($attribute);

        $results = $repository->findBy(['model' => $model]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Attribute::class, $result);
            $this->assertSame($model->getId(), $result->getModel()?->getId());
        }
    }

    public function testFindByWithNullModel(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $attribute = $this->createTestAttribute();
        $attribute->setModel(null);
        $repository->save($attribute);

        $results = $repository->findBy(['model' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Attribute::class, $result);
            $this->assertNull($result->getModel());
        }
    }

    public function testCountWithModelAssociation(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $modelRepository = self::getService(ModelRepository::class);

        // 创建模型
        $model = new Model();
        $model->setTitle('Test Model for Count');
        $model->setCode('test_model_count');
        $model->setValid(true);
        $modelRepository->save($model);

        // 创建属性并关联模型
        $attribute = $this->createTestAttribute();
        $attribute->setModel($model);
        $repository->save($attribute);

        $count = $repository->count(['model' => $model]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    // 可空字段 IS NULL 查询测试
    public function testFindByWithNullDefaultValue(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $attribute = $this->createTestAttribute();
        $attribute->setDefaultValue(null);
        $repository->save($attribute);

        $results = $repository->findBy(['defaultValue' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Attribute::class, $result);
            $this->assertNull($result->getDefaultValue());
        }
    }

    public function testFindByWithNullRequired(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $attribute = $this->createTestAttribute();
        $attribute->setRequired(null);
        $repository->save($attribute);

        $results = $repository->findBy(['required' => null]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Attribute::class, $result);
        }
    }

    public function testCountWithNullDefaultValue(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $attribute = $this->createTestAttribute();
        $attribute->setDefaultValue(null);
        $repository->save($attribute);

        $count = $repository->count(['defaultValue' => null]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithNullRequired(): void
    {
        $repository = self::getService(AttributeRepository::class);
        $attribute = $this->createTestAttribute();
        $attribute->setRequired(null);
        $repository->save($attribute);

        $count = $repository->count(['required' => null]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    protected function onSetUp(): void
    {
    }

    /**
     * @return ServiceEntityRepository<Attribute>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(AttributeRepository::class);
    }

    protected function createNewEntity(): object
    {
        $attribute = new Attribute();
        $attribute->setName('test-attribute-'.uniqid());
        $attribute->setTitle('Test Attribute '.uniqid());
        $attribute->setType(FieldType::STRING);
        $attribute->setValid(true);
        $attribute->setRequired(false);
        $attribute->setSearchable(false);
        $attribute->setImportable(false);
        $attribute->setSpan(12);
        $attribute->setDisplayOrder(0);

        return $attribute;
    }

    private function createTestAttribute(): Attribute
    {
        $attribute = new Attribute();
        $attribute->setName('test_attribute_'.uniqid());
        $attribute->setTitle('Test Attribute');
        $attribute->setType(FieldType::STRING);
        $attribute->setValid(true);
        $attribute->setRequired(false);
        $attribute->setSearchable(false);
        $attribute->setImportable(false);
        $attribute->setSpan(12);
        $attribute->setDisplayOrder(0);

        return $attribute;
    }
}
