<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\EventSubscriber;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Tourze\CmsBundle\Entity\Entity;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Entity::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Entity::class)]
final class EntityListener
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
     * 检查开始时间和结束时间.
     */
    private function ensureSameAccount(Entity $object): void
    {
        if (null === $object->getPublishTime() || null === $object->getEndTime()) {
            return;
        }

        $startTime = CarbonImmutable::parse($object->getPublishTime());
        $endTime = CarbonImmutable::parse($object->getEndTime());
        if ($startTime->greaterThan($endTime)) {
            throw new \InvalidArgumentException('发布时间不应该大于结束时间');
        }
    }
}
