<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\LikeLog;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class LikeLogTest extends TestCase
{
    private LikeLog $likeLog;

    protected function setUp(): void
    {
        $this->likeLog = new LikeLog();
    }

    public function testGettersAndSetters(): void
    {
        $user = $this->createMock(UserInterface::class);
        $entity = $this->createMock(Entity::class);
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        $createdBy = 'user123';
        $updatedBy = 'user456';
        $valid = true;

        $this->likeLog->setUser($user);
        $this->likeLog->setEntity($entity);
        $this->likeLog->setCreatedFromIp($createdFromIp);
        $this->likeLog->setUpdatedFromIp($updatedFromIp);
        $this->likeLog->setCreatedBy($createdBy);
        $this->likeLog->setUpdatedBy($updatedBy);
        $this->likeLog->setValid($valid);

        $this->assertSame($user, $this->likeLog->getUser());
        $this->assertSame($entity, $this->likeLog->getEntity());
        $this->assertSame($createdFromIp, $this->likeLog->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->likeLog->getUpdatedFromIp());
        $this->assertSame($createdBy, $this->likeLog->getCreatedBy());
        $this->assertSame($updatedBy, $this->likeLog->getUpdatedBy());
        $this->assertSame($valid, $this->likeLog->isValid());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->likeLog);
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->likeLog->getId());
        $this->assertNull($this->likeLog->getUser());
        $this->assertNull($this->likeLog->getEntity());
        $this->assertNull($this->likeLog->getCreatedFromIp());
        $this->assertNull($this->likeLog->getUpdatedFromIp());
        $this->assertFalse($this->likeLog->isValid()); // 默认值是false
    }

    public function testSetUserWithNull(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->likeLog->setUser($user);
        $this->likeLog->setUser(null);

        $this->assertNull($this->likeLog->getUser());
    }

    public function testSetEntityWithNull(): void
    {
        $entity = $this->createMock(Entity::class);
        $this->likeLog->setEntity($entity);
        $this->likeLog->setEntity(null);

        $this->assertNull($this->likeLog->getEntity());
    }

    public function testSetValidWithNull(): void
    {
        $this->likeLog->setValid(true);
        $this->likeLog->setValid(null);

        $this->assertNull($this->likeLog->isValid());
    }

    public function testValidStateTransitions(): void
    {
        // 测试有效状态的转换
        $this->likeLog->setValid(true);
        $this->assertTrue($this->likeLog->isValid());

        $this->likeLog->setValid(false);
        $this->assertFalse($this->likeLog->isValid());
    }

    public function testFluentInterface(): void
    {
        $user = $this->createMock(UserInterface::class);
        $entity = $this->createMock(Entity::class);
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        $createdBy = 'user123';
        $updatedBy = 'user456';
        $valid = true;

        $result = $this->likeLog
            ->setUser($user)
            ->setEntity($entity)
            ->setCreatedFromIp($createdFromIp)
            ->setUpdatedFromIp($updatedFromIp)
            ->setCreatedBy($createdBy)
            ->setUpdatedBy($updatedBy)
            ->setValid($valid);

        $this->assertSame($this->likeLog, $result);
    }
}
