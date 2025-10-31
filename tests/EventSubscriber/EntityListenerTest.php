<?php

declare(strict_types=1);

namespace CmsBundle\Tests\EventSubscriber;

use CmsBundle\EventSubscriber\EntityListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(EntityListener::class)]
#[RunTestsInSeparateProcesses]
final class EntityListenerTest extends AbstractIntegrationTestCase
{
    private EntityListener $listener;

    public function testPrePersistMethodExists(): void
    {
        $this->listener = self::getService(EntityListener::class);
        $reflection = new \ReflectionMethod($this->listener, 'prePersist');
        $this->assertTrue($reflection->isPublic());
    }

    public function testPreUpdateMethodExists(): void
    {
        $this->listener = self::getService(EntityListener::class);
        $reflection = new \ReflectionMethod($this->listener, 'preUpdate');
        $this->assertTrue($reflection->isPublic());
    }

    protected function onSetUp(): void
    {
        // 保持兼容性的空方法
    }
}
