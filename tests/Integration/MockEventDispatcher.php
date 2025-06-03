<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * 模拟事件调度器
 */
class MockEventDispatcher extends EventDispatcher
{
    /**
     * 已经调度的事件
     */
    private array $dispatchedEvents = [];

    /**
     * 调度事件
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        $this->dispatchedEvents[] = [
            'event' => $event,
            'eventName' => $eventName,
        ];
        
        return $event;
    }

    /**
     * 获取已经调度的事件
     */
    public function getDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }

    /**
     * 清空事件记录
     */
    public function reset(): void
    {
        $this->dispatchedEvents = [];
    }
} 