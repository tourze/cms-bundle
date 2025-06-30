<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\ShareLog;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ShareLogTest extends TestCase
{
    private ShareLog $shareLog;

    protected function setUp(): void
    {
        $this->shareLog = new ShareLog();
    }

    public function testGettersAndSetters(): void
    {
        $user = $this->createMock(UserInterface::class);
        $entity = $this->createMock(Entity::class);
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->shareLog->setUser($user);
        $this->shareLog->setEntity($entity);
        $this->shareLog->setCreatedFromIp($createdFromIp);
        $this->shareLog->setUpdatedFromIp($updatedFromIp);

        $this->assertSame($user, $this->shareLog->getUser());
        $this->assertSame($entity, $this->shareLog->getEntity());
        $this->assertSame($createdFromIp, $this->shareLog->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->shareLog->getUpdatedFromIp());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->shareLog);
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->shareLog->getId());
        $this->assertNull($this->shareLog->getUser());
        $this->assertNull($this->shareLog->getEntity());
        $this->assertNull($this->shareLog->getCreatedFromIp());
        $this->assertNull($this->shareLog->getUpdatedFromIp());
    }

    public function testSetUserWithNull(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->shareLog->setUser($user);
        $this->shareLog->setUser(null);

        $this->assertNull($this->shareLog->getUser());
    }

    public function testSetEntityWithNull(): void
    {
        $entity = $this->createMock(Entity::class);
        $this->shareLog->setEntity($entity);
        $this->shareLog->setEntity(null);

        $this->assertNull($this->shareLog->getEntity());
    }

    public function testFluentInterface(): void
    {
        $user = $this->createMock(UserInterface::class);
        $entity = $this->createMock(Entity::class);
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $result = $this->shareLog
            ->setUser($user)
            ->setEntity($entity)
            ->setCreatedFromIp($createdFromIp)
            ->setUpdatedFromIp($updatedFromIp);

        $this->assertSame($this->shareLog, $result);
    }
}
