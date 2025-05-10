<?php

namespace CmsBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;
use Tourze\LockServiceBundle\Model\LockEntity;
use Tourze\LockServiceBundle\Service\LockService;

/**
 * 模拟EntityLockService
 */
class MockEntityLockService extends EntityLockService
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ?LockService $lockService = null
    ) {
        if ($lockService === null) {
            $lockService = new MockLockService();
        }
        parent::__construct($entityManager, $lockService);
    }
    
    /**
     * 锁定单个实体，简化版本
     */
    public function lockEntity(LockEntity $entity, callable $func): mixed
    {
        // 简单地直接调用回调函数而不做锁定
        return call_user_func_array($func, []);
    }
    
    /**
     * 锁定多个实体，简化版本
     *
     * @param LockEntity[] $entities
     */
    public function lockEntities(array $entities, callable $func): mixed
    {
        // 简单地直接调用回调函数而不做锁定
        return call_user_func_array($func, []);
    }
} 