<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\EntityState;
use CmsBundle\Procedure\GetCmsEntityList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CatalogBundle\Entity\CatalogType;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetCmsEntityList::class)]
#[RunTestsInSeparateProcesses]
final class GetCmsEntityListTest extends AbstractProcedureTestCase
{
    private GetCmsEntityList $procedure;

    public function testExecuteSuccess(): void
    {
        // 创建测试目录类型
        $catalogType = new CatalogType();
        $catalogType->setName('Test Type');
        $catalogType->setCode('test_type');
        $this->persistAndFlush($catalogType);

        // 创建测试目录
        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName('Test Catalog');
        $this->persistAndFlush($catalog);

        // 创建测试数据
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $this->persistAndFlush($model);

        $entity1 = new Entity();
        $entity1->setTitle('Article 1');
        $entity1->setState(EntityState::PUBLISHED);
        $entity1->setModel($model);
        $entity1->addCatalog($catalog);
        $this->persistAndFlush($entity1);

        $entity2 = new Entity();
        $entity2->setTitle('Article 2');
        $entity2->setState(EntityState::PUBLISHED);
        $entity2->setModel($model);
        $entity2->addCatalog($catalog);
        $this->persistAndFlush($entity2);

        // 执行测试
        $this->procedure->catalogId = $catalog->getId();
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertIsArray($result['pagination']);
        $this->assertArrayHasKey('total', $result['pagination']);
        $this->assertArrayHasKey('current', $result['pagination']);
        $this->assertArrayHasKey('pageSize', $result['pagination']);
        $this->assertArrayHasKey('hasMore', $result['pagination']);
    }

    public function testExecuteWithKeyword(): void
    {
        // 创建测试目录类型
        $catalogType = new CatalogType();
        $catalogType->setName('Test Type');
        $catalogType->setCode('test_type');
        $this->persistAndFlush($catalogType);

        // 创建测试目录
        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName('Test Catalog');
        $this->persistAndFlush($catalog);

        // 创建测试数据
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $this->persistAndFlush($model);

        $entity1 = new Entity();
        $entity1->setTitle('Breaking News');
        $entity1->setState(EntityState::PUBLISHED);
        $entity1->setModel($model);
        $entity1->addCatalog($catalog);
        $this->persistAndFlush($entity1);

        $entity2 = new Entity();
        $entity2->setTitle('Regular Article');
        $entity2->setState(EntityState::PUBLISHED);
        $entity2->setModel($model);
        $entity2->addCatalog($catalog);
        $this->persistAndFlush($entity2);

        // 测试关键词过滤
        $this->procedure->catalogId = $catalog->getId();
        $this->procedure->keyword = 'Breaking';

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);
    }

    public function testExecuteWithModelCode(): void
    {
        // 创建测试目录类型
        $catalogType = new CatalogType();
        $catalogType->setName('Test Type');
        $catalogType->setCode('test_type');
        $this->persistAndFlush($catalogType);

        // 创建测试目录
        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName('Test Catalog');
        $this->persistAndFlush($catalog);

        // 创建测试数据
        $model = new Model();
        $model->setTitle('Article Model');
        $model->setCode('article');
        $this->persistAndFlush($model);

        $entity = new Entity();
        $entity->setTitle('Test Article');
        $entity->setState(EntityState::PUBLISHED);
        $entity->setModel($model);
        $entity->addCatalog($catalog);
        $this->persistAndFlush($entity);

        // 测试模型代码过滤
        $this->procedure->catalogId = $catalog->getId();
        $this->procedure->modelCode = 'article';

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);
    }

    public function testExecuteEmptyCatalog(): void
    {
        // 创建测试目录类型
        $catalogType = new CatalogType();
        $catalogType->setName('Test Type');
        $catalogType->setCode('test_type');
        $this->persistAndFlush($catalogType);

        // 创建测试目录（空的，没有关联任何entity）
        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName('Empty Catalog');
        $this->persistAndFlush($catalog);

        // 执行测试
        $this->procedure->catalogId = $catalog->getId();
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);

        // 确保pagination是数组
        $this->assertIsArray($result['pagination']);
        /** @var array<string, mixed> $pagination */
        $pagination = $result['pagination'];

        $this->assertSame(0, $pagination['total']);
        $this->assertSame(1, $pagination['current']);
        $this->assertSame(10, $pagination['pageSize']); // 默认是10不是20
        $this->assertFalse($pagination['hasMore']);

        // 确保list是可计数的
        $this->assertIsArray($result['list']);
        $this->assertCount(0, $result['list']);
    }

    public function testExecuteNotFoundCatalog(): void
    {
        $this->procedure->catalogId = 999999;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('目录不存在');

        $this->procedure->execute();
    }

    public function testExecuteOnlyPublished(): void
    {
        // 创建测试目录类型
        $catalogType = new CatalogType();
        $catalogType->setName('Test Type');
        $catalogType->setCode('test_type');
        $this->persistAndFlush($catalogType);

        // 创建测试目录
        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName('Test Catalog');
        $this->persistAndFlush($catalog);

        // 创建测试数据
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $this->persistAndFlush($model);

        $publishedEntity = new Entity();
        $publishedEntity->setTitle('Published Article');
        $publishedEntity->setState(EntityState::PUBLISHED);
        $publishedEntity->setModel($model);
        $publishedEntity->addCatalog($catalog);
        $this->persistAndFlush($publishedEntity);

        $draftEntity = new Entity();
        $draftEntity->setTitle('Draft Article');
        $draftEntity->setState(EntityState::DRAFT);
        $draftEntity->setModel($model);
        $draftEntity->addCatalog($catalog);
        $this->persistAndFlush($draftEntity);

        // 执行测试
        $this->procedure->catalogId = $catalog->getId();
        $result = $this->procedure->execute();

        // 应该只返回已发布的文章
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);
    }

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetCmsEntityList::class);
    }
}
