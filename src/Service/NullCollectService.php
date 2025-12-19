<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\CmsBundle\Entity\Entity;

/**
 * 收藏服务的空实现.
 *
 * 当 cms-collect-bundle 未安装时，提供默认的空实现，始终返回未收藏状态。
 */
readonly class NullCollectService implements CollectServiceInterface
{
    public function isCollectedByUser(Entity $entity, UserInterface $user): bool
    {
        return false;
    }
}
