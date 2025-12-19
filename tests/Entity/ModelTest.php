<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Entity\Attribute;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Entity\Model;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Model::class)]
final class ModelTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $model = new Model();
        $this->assertInstanceOf(Model::class, $model);
    }

    public function testSettersAndGetters(): void
    {
        $model = new Model();

        $model->setCode('test-code');
        $this->assertSame('test-code', $model->getCode());

        $model->setTitle('Test Title');
        $this->assertSame('Test Title', $model->getTitle());

        $model->setAllowLike(true);
        $this->assertTrue($model->getAllowLike());

        $model->setAllowCollect(true);
        $this->assertTrue($model->getAllowCollect());

        $model->setAllowShare(true);
        $this->assertTrue($model->getAllowShare());

        $model->setSortNumber(10);
        $this->assertSame(10, $model->getSortNumber());

        $sorts = ['field' => 'asc'];
        $model->setContentSorts($sorts);
        $this->assertSame($sorts, $model->getContentSorts());

        $model->setTopicSorts($sorts);
        $this->assertSame($sorts, $model->getTopicSorts());

        $model->setValid(true);
        $this->assertTrue($model->isValid());
    }

    public function testAttributeRelations(): void
    {
        $model = new Model();
        $attribute = new Attribute();

        $model->addAttribute($attribute);
        $this->assertTrue($model->getAttributes()->contains($attribute));
        $this->assertSame($model, $attribute->getModel());

        $model->removeAttribute($attribute);
        $this->assertFalse($model->getAttributes()->contains($attribute));
    }

    public function testEntityRelations(): void
    {
        $model = new Model();
        $entity = new Entity();

        $model->addEntity($entity);
        $this->assertTrue($model->getEntities()->contains($entity));
        $this->assertSame($model, $entity->getModel());

        $model->removeEntity($entity);
        $this->assertFalse($model->getEntities()->contains($entity));
    }

    public function testToString(): void
    {
        $model = new Model();
        $model->setTitle('Test Title');
        $model->setCode('test-code');

        $this->assertSame('Test Title', (string) $model);

        $emptyModel = new Model();
        $this->assertSame('', (string) $emptyModel);
    }

    public function testRetrieveAdminArray(): void
    {
        $model = new Model();
        $model->setCode('test-code');
        $model->setTitle('Test Title');

        $array = $model->retrieveAdminArray();
        $this->assertArrayHasKey('code', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertSame('test-code', $array['code']);
        $this->assertSame('Test Title', $array['title']);
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'code' => ['code', 'test-model'];
        yield 'title' => ['title', 'Test Model'];
        yield 'sortNumber' => ['sortNumber', 100];
        yield 'valid' => ['valid', true];
    }

    protected function createEntity(): Model
    {
        return new Model();
    }
}
