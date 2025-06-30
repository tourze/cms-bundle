<?php

namespace CmsBundle\Tests\Event;

use CmsBundle\Entity\Entity;
use CmsBundle\Event\LikeEntityEvent;
use PHPUnit\Framework\TestCase;

class LikeEntityEventTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $event = new LikeEntityEvent();
        $entity = $this->createMock(Entity::class);

        $event->setEntity($entity);

        $this->assertSame($entity, $event->getEntity());
    }
}
