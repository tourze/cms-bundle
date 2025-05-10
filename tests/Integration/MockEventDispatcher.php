<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * 模拟事件调度器
 */
class MockEventDispatcher implements EventDispatcherInterface
{
    /**
     * 记录的事件
     */
    private array $events = [];
    
    /**
     * 事件监听器
     */
    private array $listeners = [];
    
    /**
     * 监听器优先级
     */
    private array $priorities = [];

    /**
     * 调度事件
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        $eventName = $eventName ?? get_class($event);
        
        $this->events[] = [
            'event' => $event,
            'name' => $eventName,
        ];
        
        // 调用相应的监听器
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                call_user_func($listener, $event);
            }
        }
        
        return $event;
    }

    /**
     * 添加事件监听器
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][] = $listener;
        $this->priorities[$eventName][spl_object_hash($listener)] = $priority;
    }

    /**
     * 添加订阅者
     */
    public function addSubscriber(\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);
            } elseif (is_array($params)) {
                foreach ($params as $listener) {
                    if (is_array($listener)) {
                        $this->addListener($eventName, [$subscriber, $listener[0]], $listener[1] ?? 0);
                    } else {
                        $this->addListener($eventName, [$subscriber, $listener]);
                    }
                }
            }
        }
    }

    /**
     * 移除监听器
     */
    public function removeListener(string $eventName, callable $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        
        $this->listeners[$eventName] = array_filter(
            $this->listeners[$eventName],
            function ($item) use ($listener) {
                return $item !== $listener;
            }
        );
        
        if (isset($this->priorities[$eventName])) {
            unset($this->priorities[$eventName][spl_object_hash($listener)]);
        }
    }

    /**
     * 移除订阅者
     */
    public function removeSubscriber(\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->removeListener($eventName, [$subscriber, $params]);
            } elseif (is_array($params)) {
                foreach ($params as $listener) {
                    if (is_array($listener)) {
                        $this->removeListener($eventName, [$subscriber, $listener[0]]);
                    } else {
                        $this->removeListener($eventName, [$subscriber, $listener]);
                    }
                }
            }
        }
    }

    /**
     * 获取事件监听器
     */
    public function getListeners(?string $eventName = null): array
    {
        if ($eventName === null) {
            return $this->listeners;
        }
        
        return $this->listeners[$eventName] ?? [];
    }
    
    /**
     * 获取监听器优先级
     */
    public function getListenerPriority(string $eventName, callable $listener): ?int
    {
        if (!isset($this->priorities[$eventName])) {
            return null;
        }
        
        return $this->priorities[$eventName][spl_object_hash($listener)] ?? null;
    }

    /**
     * 检查是否有事件监听器
     */
    public function hasListeners(?string $eventName = null): bool
    {
        if ($eventName === null) {
            return !empty($this->listeners);
        }
        
        return isset($this->listeners[$eventName]) && !empty($this->listeners[$eventName]);
    }
    
    /**
     * 获取记录的事件
     */
    public function getEvents(): array
    {
        return $this->events;
    }
    
    /**
     * 清空记录的事件
     */
    public function reset(): void
    {
        $this->events = [];
    }
} 