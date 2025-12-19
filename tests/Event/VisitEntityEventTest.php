<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Event\VisitEntityEvent;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(VisitEntityEvent::class)]
final class VisitEntityEventTest extends AbstractEventTestCase
{
    public function testGettersAndSetters(): void
    {
        $event = new VisitEntityEvent();

        // 直接使用 Entity 实例进行测试
        $entity = new Entity();
        $entity->setTitle('Test Entity');

        $event->setEntity($entity);

        $this->assertSame($entity, $event->getEntity());
    }
}
