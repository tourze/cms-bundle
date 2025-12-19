<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CatalogBundle\Entity\CatalogType;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Param\GetCmsEntityListParam;
use Tourze\CmsBundle\Procedure\GetCmsEntityList;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

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
        $result = $this->procedure->execute(new GetCmsEntityListParam(catalogId: $catalog->getId()));
        $data = $result->data;

        // 验证结果
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertIsArray($data['pagination']);
        $this->assertArrayHasKey('total', $data['pagination']);
        $this->assertArrayHasKey('current', $data['pagination']);
        $this->assertArrayHasKey('pageSize', $data['pagination']);
        $this->assertArrayHasKey('hasMore', $data['pagination']);
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
        $result = $this->procedure->execute(new GetCmsEntityListParam(
            catalogId: $catalog->getId(),
            keyword: 'Breaking'
        ));
        $data = $result->data;

        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
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
        $result = $this->procedure->execute(new GetCmsEntityListParam(
            catalogId: $catalog->getId(),
            modelCode: 'article'
        ));
        $data = $result->data;

        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
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
        $result = $this->procedure->execute(new GetCmsEntityListParam(catalogId: $catalog->getId()));
        $data = $result->data;

        // 验证结果
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);

        // 确保pagination是数组
        $this->assertIsArray($data['pagination']);
        /** @var array<string, mixed> $pagination */
        $pagination = $data['pagination'];

        $this->assertSame(0, $pagination['total']);
        $this->assertSame(1, $pagination['current']);
        $this->assertSame(10, $pagination['pageSize']); // 默认是10不是20
        $this->assertFalse($pagination['hasMore']);

        // 确保list是可计数的
        $this->assertIsArray($data['list']);
        $this->assertCount(0, $data['list']);
    }

    public function testExecuteNotFoundCatalog(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('目录不存在');

        $this->procedure->execute(new GetCmsEntityListParam(catalogId: 999999));
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
        $result = $this->procedure->execute(new GetCmsEntityListParam(catalogId: $catalog->getId()));
        $data = $result->data;

        // 应该只返回已发布的文章
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetCmsEntityList::class);
    }
}
