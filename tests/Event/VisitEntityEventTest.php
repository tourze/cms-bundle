<?php

namespace CmsBundle\Tests\Event;

use CmsBundle\Entity\Entity;
use CmsBundle\Event\VisitEntityEvent;
use PHPUnit\Framework\TestCase;

class VisitEntityEventTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $event = new VisitEntityEvent();
        $entity = $this->createMock(Entity::class);

        $event->setEntity($entity);

        $this->assertSame($entity, $event->getEntity());
    }
}
