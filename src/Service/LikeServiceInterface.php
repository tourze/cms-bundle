<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\CmsBundle\Entity\Entity;

/**
 * 点赞服务接口.
 *
 * 此接口定义了点赞服务的契约，允许 cms-bundle 在不直接依赖 cms-like-bundle 的情况下使用点赞功能。
 * cms-like-bundle 可以通过服务装饰提供真实实现。
 */
interface LikeServiceInterface
{
    /**
     * 判断用户是否对指定实体点过赞.
     */
    public function isLikedByUser(Entity $entity, UserInterface $user): bool;
}
