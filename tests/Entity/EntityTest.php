<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Entity\Value;
use CmsBundle\Enum\EntityState;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * Entity 单元测试（简化版，避免外部依赖）.
 *
 * @internal
 */
#[CoversClass(Entity::class)]
final class EntityTest extends AbstractEntityTestCase
{
    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'title' => ['title', 'Test Entity'];
        yield 'sortNumber' => ['sortNumber', 100];
        yield 'state' => ['state', EntityState::DRAFT];
    }

    public function testEntityCreation(): void
    {
        $entity = new Entity();
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertNull($entity->getId());
    }

    public function testTitleProperty(): void
    {
        $entity = new Entity();
        $entity->setTitle('Test Title');
        $this->assertSame('Test Title', $entity->getTitle());
    }

    public function testModelProperty(): void
    {
        $entity = new Entity();
        $model = new Model();

        $entity->setModel($model);
        $this->assertSame($model, $entity->getModel());

        $entity->setModel(null);
        $this->assertNull($entity->getModel());
    }

    public function testPublishTimeProperty(): void
    {
        $entity = new Entity();
        $publishTime = new \DateTimeImmutable();

        $entity->setPublishTime($publishTime);
        $this->assertSame($publishTime, $entity->getPublishTime());

        $entity->setPublishTime(null);
        $this->assertNull($entity->getPublishTime());
    }

    public function testEndTimeProperty(): void
    {
        $entity = new Entity();
        $endTime = new \DateTimeImmutable();

        $entity->setEndTime($endTime);
        $this->assertSame($endTime, $entity->getEndTime());

        $entity->setEndTime(null);
        $this->assertNull($entity->getEndTime());
    }

    public function testSortNumberProperty(): void
    {
        $entity = new Entity();

        $entity->setSortNumber(100);
        $this->assertSame(100, $entity->getSortNumber());

        $entity->setSortNumber(null);
        $this->assertNull($entity->getSortNumber());
    }

    public function testRemarkProperty(): void
    {
        $entity = new Entity();

        $entity->setRemark('Test remark');
        $this->assertSame('Test remark', $entity->getRemark());

        $entity->setRemark(null);
        $this->assertNull($entity->getRemark());
    }

    public function testValueListCollection(): void
    {
        $entity = new Entity();
        $valueList = $entity->getValueList();

        $this->assertInstanceOf(Collection::class, $valueList);
        $this->assertCount(0, $valueList);
    }

    public function testAddRemoveValueList(): void
    {
        $entity = new Entity();
        $value = new Value();

        $entity->addValueList($value);
        $this->assertCount(1, $entity->getValueList());
        $this->assertTrue($entity->getValueList()->contains($value));

        $entity->removeValueList($value);
        $this->assertCount(0, $entity->getValueList());
        $this->assertFalse($entity->getValueList()->contains($value));
    }

    public function testStringRepresentation(): void
    {
        $entity = new Entity();

        // 没有 ID 时返回空字符串
        $this->assertSame('', (string) $entity);
    }

    public function testStringRepresentationWithData(): void
    {
        $entity = new Entity();
        $entity->setTitle('Test Title');

        // 仍然没有 ID，所以返回空字符串
        $this->assertSame('', (string) $entity);
    }

    public function testGetValuesMethod(): void
    {
        $entity = new Entity();
        $values = $entity->getValues();

        $this->assertIsArray($values);
        $this->assertEmpty($values);
    }

    protected function createEntity(): Entity
    {
        return new Entity();
    }
}
