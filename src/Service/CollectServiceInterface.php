<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\CmsBundle\Entity\Entity;

/**
 * 收藏服务接口.
 *
 * 此接口定义了收藏服务的契约，允许 cms-bundle 在不直接依赖 cms-collect-bundle 的情况下使用收藏功能。
 * cms-collect-bundle 可以通过服务装饰提供真实实现。
 */
interface CollectServiceInterface
{
    /**
     * 判断用户是否收藏了指定实体.
     */
    public function isCollectedByUser(Entity $entity, UserInterface $user): bool;
}
