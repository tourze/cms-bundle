<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\Lock\LockInterface;
use Tourze\LockServiceBundle\Model\LockEntity;
use Tourze\LockServiceBundle\Service\LockService;

/**
 * 模拟锁服务
 */
class MockLockService extends LockService
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        // 空构造函数，覆盖父类的构造函数
    }
    
    /**
     * 带锁执行指定逻辑（模拟实现，直接执行回调）
     */
    public function blockingRun(LockEntity|string|array $entity, callable $callback): mixed
    {
        // 在测试环境中直接执行回调，不进行实际加锁
        return call_user_func($callback);
    }

    /**
     * 请求级别加锁（模拟实现）
     */
    public function acquireLock(string $key): LockInterface
    {
        return new MockLock($key);
    }

    /**
     * 请求级别释放锁（模拟实现）
     */
    public function releaseLock(string $key): void
    {
        // 模拟释放锁，不做任何操作
    }

    /**
     * 自动释放所有锁（模拟实现）
     */
    public function reset(): void
    {
        // 模拟重置，不做任何操作
    }
}

/**
 * 模拟锁接口实现
 */
class MockLock implements LockInterface
{
    private bool $acquired = false;
    
    public function __construct(private readonly string $key)
    {
    }

    public function acquire(bool $blocking = false): bool
    {
        $this->acquired = true;
        return true;
    }

    public function refresh(?float $ttl = null): void
    {
        // 模拟刷新，不做任何操作
    }

    public function isAcquired(): bool
    {
        return $this->acquired;
    }

    public function release(): void
    {
        $this->acquired = false;
    }

    public function isExpired(): bool
    {
        return false;
    }

    public function getRemainingLifetime(): ?float
    {
        return 300.0; // 返回固定的300秒
    }
} 