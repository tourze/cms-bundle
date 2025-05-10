<?php

namespace CmsBundle\Tests\Integration;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * 模拟消息总线
 */
class MockMessageBus implements MessageBusInterface
{
    /**
     * 已经发送的消息
     */
    private array $dispatchedMessages = [];
    
    /**
     * 发送消息
     */
    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $this->dispatchedMessages[] = [
            'message' => $message,
            'stamps' => $stamps,
        ];
        
        return new Envelope($message, $stamps);
    }
    
    /**
     * 获取已经发送的消息
     */
    public function getDispatchedMessages(): array
    {
        return $this->dispatchedMessages;
    }
    
    /**
     * 清空消息记录
     */
    public function reset(): void
    {
        $this->dispatchedMessages = [];
    }
} 