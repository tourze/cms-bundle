<?php

namespace CmsBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;

/**
 * 模拟Doctrine服务
 */
class MockDoctrineService extends DoctrineService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        // 覆盖父类构造函数
    }

    /**
     * 获取实体管理器
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * 处理事务
     */
    public function transactional(callable $func): mixed
    {
        return call_user_func($func, $this->entityManager);
    }

    /**
     * 刷新实体管理器
     */
    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * 清除实体管理器
     */
    public function clear(): void
    {
        $this->entityManager->clear();
    }
} 