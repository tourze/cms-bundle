<?php

declare(strict_types=1);

namespace CmsBundle\Event;

use CmsBundle\Entity\Entity;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

class CollectEntityEvent extends UserInteractionEvent
{
    private Entity $entity;

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public static function getTitle(): string
    {
        return 'CMS - 内容收藏成功';
    }
}
