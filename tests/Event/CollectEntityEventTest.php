<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsBundle\Event\CollectEntityEvent;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(CollectEntityEvent::class)]
final class CollectEntityEventTest extends AbstractEventTestCase
{
    public function testGettersAndSetters(): void
    {
        $event = new CollectEntityEvent();

        // 直接使用 Entity 实例进行测试
        $entity = new Entity();
        $entity->setTitle('Test Entity');

        $event->setEntity($entity);

        $this->assertSame($entity, $event->getEntity());
    }

    public function testGetTitle(): void
    {
        $this->assertSame('CMS - 内容收藏成功', CollectEntityEvent::getTitle());
    }
}
