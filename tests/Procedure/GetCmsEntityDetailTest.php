<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Param\GetCmsEntityDetailParam;
use Tourze\CmsBundle\Procedure\GetCmsEntityDetail;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetCmsEntityDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetCmsEntityDetailTest extends AbstractProcedureTestCase
{
    private GetCmsEntityDetail $procedure;

    public function testExecuteSuccess(): void
    {
        // 创建测试数据
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $this->persistAndFlush($model);

        $entity = new Entity();
        $entity->setTitle('Test Article');
        $entity->setState(EntityState::PUBLISHED);
        $entity->setModel($model);
        $this->persistAndFlush($entity);

        // 执行测试
        $entityId = $entity->getId();
        $this->assertNotNull($entityId, 'Entity ID should not be null');
        $result = $this->procedure->execute(new GetCmsEntityDetailParam(entityId: $entityId));
        $data = $result->data;

        // 验证结果
        $this->assertArrayHasKey('id', $data);
        $this->assertSame($entity->getId(), $data['id']);
        $this->assertSame('Test Article', $data['title']);
        $this->assertSame(EntityState::PUBLISHED->value, $data['state']);
    }

    public function testExecuteNotFound(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('记录不存在');

        $this->procedure->execute(new GetCmsEntityDetailParam(entityId: 999999));
    }

    public function testExecuteNotPublished(): void
    {
        // 创建测试数据
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $this->persistAndFlush($model);

        $entity = new Entity();
        $entity->setTitle('Draft Article');
        $entity->setState(EntityState::DRAFT);
        $entity->setModel($model);
        $this->persistAndFlush($entity);

        // 执行测试
        $entityId = $entity->getId();
        $this->assertNotNull($entityId, 'Entity ID should not be null');

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('记录不存在');

        $this->procedure->execute(new GetCmsEntityDetailParam(entityId: $entityId));
    }

    public function testExecuteWithUser(): void
    {
        // 创建测试数据
        $model = new Model();
        $model->setTitle('Test Model');
        $model->setCode('test_model');
        $this->persistAndFlush($model);

        $entity = new Entity();
        $entity->setTitle('User Article');
        $entity->setState(EntityState::PUBLISHED);
        $entity->setModel($model);
        $this->persistAndFlush($entity);

        // 执行测试（不设置认证用户，测试匿名访问）
        $entityId = $entity->getId();
        $this->assertNotNull($entityId, 'Entity ID should not be null');
        $result = $this->procedure->execute(new GetCmsEntityDetailParam(entityId: $entityId));
        $data = $result->data;

        // 验证结果
        $this->assertArrayHasKey('id', $data);
        $this->assertSame($entity->getId(), $data['id']);
        $this->assertSame('User Article', $data['title']);
        $this->assertArrayHasKey('isLike', $data);
        $this->assertFalse($data['isLike']); // 匿名用户默认为false
    }

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetCmsEntityDetail::class);
    }
}
