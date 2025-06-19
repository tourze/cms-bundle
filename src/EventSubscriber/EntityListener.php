<?php

namespace CmsBundle\EventSubscriber;

use Carbon\CarbonImmutable;
use CmsBundle\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Tourze\JsonRPC\Core\Exception\ApiException;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Entity::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Entity::class)]
class EntityListener
{
    public function prePersist(Entity $object): void
    {
        $this->ensureSameAccount($object);
    }

    public function preUpdate(Entity $object): void
    {
        $this->ensureSameAccount($object);
    }

    /**
     * 检查开始时间和结束时间
     */
    private function ensureSameAccount(Entity $object): void
    {
        if ($object->getPublishTime() === null || $object->getEndTime() === null) {
            return;
        }

        $startTime = CarbonImmutable::parse($object->getPublishTime());
        $endTime = CarbonImmutable::parse($object->getEndTime());
        if ($startTime->greaterThan($endTime)) {
            throw new ApiException('发布时间不应该大于结束时间');
        }
    }
}
