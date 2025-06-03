<?php

namespace CmsBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;
use Tourze\LockServiceBundle\Model\LockEntity;
use Tourze\LockServiceBundle\Service\LockService;

/**
 * 模拟实体锁服务
 */
class MockEntityLockService extends EntityLockService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LockService $lockService
    ) {
        // 覆盖父类构造函数
    }

    /**
     * 锁定单个实体，从数据库读取最新数据，然后执行回调
     */
    public function lockEntity(LockEntity $entity, callable $func): mixed
    {
        // 在测试环境中直接执行回调，不进行实际加锁
        return call_user_func($func);
    }

    /**
     * 锁定多个实体，从数据库读取最新数据，然后执行回调
     *
     * @param LockEntity[] $entities
     */
    public function lockEntities(array $entities, callable $func): mixed
    {
        // 在测试环境中直接执行回调，不进行实际加锁
        return call_user_func($func);
    }
} 