<?php

namespace CmsBundle\Event;

use CmsBundle\Entity\Entity;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

class VisitEntityEvent extends UserInteractionEvent
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
