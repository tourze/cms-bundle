<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\EntityState;
use CmsBundle\Procedure\GetCmsEntityDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

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
        $this->procedure->entityId = $entityId;
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertArrayHasKey('id', $result);
        $this->assertSame($entity->getId(), $result['id']);
        $this->assertSame('Test Article', $result['title']);
        $this->assertSame(EntityState::PUBLISHED->value, $result['state']);
    }

    public function testExecuteNotFound(): void
    {
        $this->procedure->entityId = 999999;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('记录不存在');

        $this->procedure->execute();
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
        $this->procedure->entityId = $entityId;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('记录不存在');

        $this->procedure->execute();
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
        $this->procedure->entityId = $entityId;
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertArrayHasKey('id', $result);
        $this->assertSame($entity->getId(), $result['id']);
        $this->assertSame('User Article', $result['title']);
        $this->assertArrayHasKey('isLike', $result);
        $this->assertFalse($result['isLike']); // 匿名用户默认为false
    }

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetCmsEntityDetail::class);
    }
}
