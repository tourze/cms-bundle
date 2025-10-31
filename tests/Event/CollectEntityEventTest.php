<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Event;

use CmsBundle\Entity\Entity;
use CmsBundle\Event\CollectEntityEvent;
use PHPUnit\Framework\Attributes\CoversClass;
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
        /*
         * 使用 Entity 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $entity = new class extends Entity {
            public function getId(): int
            {
                return 1;
            }

            public function getName(): string
            {
                return 'Test Entity';
            }
        };

        $event->setEntity($entity);

        $this->assertSame($entity, $event->getEntity());
    }

    public function testGetTitle(): void
    {
        $this->assertSame('CMS - 内容收藏成功', CollectEntityEvent::getTitle());
    }
}
