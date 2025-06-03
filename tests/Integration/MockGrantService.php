<?php

namespace CmsBundle\Tests\Integration;

use Tourze\JsonRPCSecurityBundle\Service\GrantService;

/**
 * 模拟授权服务
 */
class MockGrantService extends GrantService
{
    public function __construct()
    {
        // 空构造函数，覆盖父类的构造函数
    }

    /**
     * 检查是否有权限
     */
    public function isGranted(string $attribute, mixed $subject = null): bool
    {
        // 在测试环境中总是返回true
        return true;
    }

    /**
     * 检查是否有角色
     */
    public function hasRole(string $role): bool
    {
        // 在测试环境中总是返回true
        return true;
    }

    /**
     * 获取当前用户
     */
    public function getUser(): ?\Symfony\Component\Security\Core\User\UserInterface
    {
        return null;
    }
}
