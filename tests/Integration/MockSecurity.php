<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 用于测试的简单Security服务实现
 */
class MockSecurity extends Security
{
    private ?UserInterface $user = null;

    public function __construct()
    {
        // 空构造函数，覆盖父类的构造函数
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function isGranted($attribute, $subject = null): bool
    {
        // 在测试环境中总是返回true
        return true;
    }

    public function getToken(): ?TokenInterface
    {
        return null;
    }
} 