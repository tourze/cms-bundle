<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\CmsBundle\Entity\Entity;

/**
 * 点赞服务的空实现.
 *
 * 当 cms-like-bundle 未安装时，提供默认的空实现，始终返回未点赞状态。
 */
readonly class NullLikeService implements LikeServiceInterface
{
    public function isLikedByUser(Entity $entity, UserInterface $user): bool
    {
        return false;
    }
}
