<?php

namespace CmsBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;

/**
 * 模拟DoctrineService
 */
class MockDoctrineService extends DoctrineService
{
    private EntityManagerInterface $em;
    
    /**
     * 构造函数
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        // 创建必要的依赖组件
        $mockLogger = new MockLogger();
        $mockMessageBus = new MockMessageBus();
        $mockSqlFormatter = new MockSqlFormatter();
        $mockDoctrineCleanSubscriber = new MockDoctrineCleanSubscriber();
        
        $this->em = $entityManager;
        
        parent::__construct(
            $entityManager, 
            $mockSqlFormatter, 
            $mockMessageBus, 
            $mockLogger, 
            $mockDoctrineCleanSubscriber
        );
    }
    
    /**
     * 执行持久化
     */
    public function persist(object $entity): void
    {
        $this->em->persist($entity);
    }
    
    /**
     * 执行刷新
     */
    public function flush(?object $entity = null): void
    {
        $this->em->flush($entity);
    }
    
    /**
     * 异步刷新（在测试中同步执行）
     */
    public function asyncFlush(?object $entity = null): void
    {
        $this->flush($entity);
    }
    
    /**
     * 手动执行冲刷操作
     */
    public function manualFlush(): void
    {
        $this->flush();
    }
    
    /**
     * 获取EntityManager实例
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
} 