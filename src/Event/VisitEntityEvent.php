<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Event;

use Tourze\CmsBundle\Entity\Entity;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

final class VisitEntityEvent extends UserInteractionEvent
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
}
