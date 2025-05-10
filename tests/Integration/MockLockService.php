<?php

namespace CmsBundle\Tests\Integration;

use Tourze\LockServiceBundle\Service\LockService;

/**
 * 模拟LockService
 */
class MockLockService extends LockService
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        // 空构造函数，不调用父类构造函数
    }
    
    /**
     * 阻塞运行
     */
    public function blockingRun($resources, callable $callback): mixed
    {
        // 直接执行回调函数，不做任何锁定
        return call_user_func_array($callback, []);
    }
} 