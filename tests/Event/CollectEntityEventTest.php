<?php

namespace CmsBundle\Tests\Event;

use CmsBundle\Entity\Entity;
use CmsBundle\Event\CollectEntityEvent;
use PHPUnit\Framework\TestCase;

class CollectEntityEventTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $event = new CollectEntityEvent();
        $entity = $this->createMock(Entity::class);

        $event->setEntity($entity);

        $this->assertSame($entity, $event->getEntity());
    }

    public function testGetTitle(): void
    {
        $this->assertSame('CMS - 内容收藏成功', CollectEntityEvent::getTitle());
    }
}
