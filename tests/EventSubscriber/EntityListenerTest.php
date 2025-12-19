<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\EventSubscriber\EntityListener;
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
