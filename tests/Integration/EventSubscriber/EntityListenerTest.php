<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Integration\EventSubscriber;

use CmsBundle\Entity\Entity;
use CmsBundle\EventSubscriber\EntityListener;
use PHPUnit\Framework\TestCase;

class EntityListenerTest extends TestCase
{
    private EntityListener $listener;

    protected function setUp(): void
    {
        $this->listener = new EntityListener();
    }

    public function testListenerInstantiation(): void
    {
        $this->assertInstanceOf(EntityListener::class, $this->listener);
    }

    public function testPrePersistMethodExists(): void
    {
        $reflection = new \ReflectionMethod($this->listener, 'prePersist');
        $this->assertTrue($reflection->isPublic());
    }

    public function testPreUpdateMethodExists(): void
    {
        $reflection = new \ReflectionMethod($this->listener, 'preUpdate');
        $this->assertTrue($reflection->isPublic());
    }
}