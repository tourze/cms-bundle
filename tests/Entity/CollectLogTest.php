<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\CollectLog;
use CmsBundle\Entity\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class CollectLogTest extends TestCase
{
    private CollectLog $collectLog;

    protected function setUp(): void
    {
        $this->collectLog = new CollectLog();
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

        $this->collectLog->setUser($user);
        $this->collectLog->setEntity($entity);
        $this->collectLog->setCreatedFromIp($createdFromIp);
        $this->collectLog->setUpdatedFromIp($updatedFromIp);
        $this->collectLog->setCreatedBy($createdBy);
        $this->collectLog->setUpdatedBy($updatedBy);
        $this->collectLog->setValid($valid);

        $this->assertSame($user, $this->collectLog->getUser());
        $this->assertSame($entity, $this->collectLog->getEntity());
        $this->assertSame($createdFromIp, $this->collectLog->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->collectLog->getUpdatedFromIp());
        $this->assertSame($createdBy, $this->collectLog->getCreatedBy());
        $this->assertSame($updatedBy, $this->collectLog->getUpdatedBy());
        $this->assertSame($valid, $this->collectLog->isValid());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->collectLog);
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->collectLog->getId());
        $this->assertNull($this->collectLog->getUser());
        $this->assertNull($this->collectLog->getEntity());
        $this->assertNull($this->collectLog->getCreatedFromIp());
        $this->assertNull($this->collectLog->getUpdatedFromIp());
        $this->assertFalse($this->collectLog->isValid()); // 默认值是false
    }

    public function testSetUserWithNull(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->collectLog->setUser($user);
        $this->collectLog->setUser(null);

        $this->assertNull($this->collectLog->getUser());
    }

    public function testSetEntityWithNull(): void
    {
        $entity = $this->createMock(Entity::class);
        $this->collectLog->setEntity($entity);
        $this->collectLog->setEntity(null);

        $this->assertNull($this->collectLog->getEntity());
    }

    public function testSetValidWithNull(): void
    {
        $this->collectLog->setValid(true);
        $this->collectLog->setValid(null);

        $this->assertNull($this->collectLog->isValid());
    }

    public function testValidStateTransitions(): void
    {
        // 测试有效状态的转换
        $this->collectLog->setValid(true);
        $this->assertTrue($this->collectLog->isValid());

        $this->collectLog->setValid(false);
        $this->assertFalse($this->collectLog->isValid());
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

        $result = $this->collectLog
            ->setUser($user)
            ->setEntity($entity)
            ->setCreatedFromIp($createdFromIp)
            ->setUpdatedFromIp($updatedFromIp)
            ->setCreatedBy($createdBy)
            ->setUpdatedBy($updatedBy)
            ->setValid($valid);

        $this->assertSame($this->collectLog, $result);
    }

    public function testCreatedByAndUpdatedBy(): void
    {
        $createdBy = 'creator';
        $updatedBy = 'updater';

        $this->collectLog->setCreatedBy($createdBy);
        $this->collectLog->setUpdatedBy($updatedBy);

        $this->assertSame($createdBy, $this->collectLog->getCreatedBy());
        $this->assertSame($updatedBy, $this->collectLog->getUpdatedBy());
    }
}
