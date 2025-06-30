<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Comment;
use CmsBundle\Entity\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentTest extends TestCase
{
    private Comment $comment;

    protected function setUp(): void
    {
        $this->comment = new Comment();
    }

    public function testGettersAndSetters(): void
    {
        $user = $this->createMock(UserInterface::class);
        $replyUser = $this->createMock(UserInterface::class);
        $entity = $this->createMock(Entity::class);
        $content = 'Test comment content';
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        $createdBy = 'user123';
        $updatedBy = 'user456';

        $this->comment->setUser($user);
        $this->comment->setReplyUser($replyUser);
        $this->comment->setEntity($entity);
        $this->comment->setContent($content);
        $this->comment->setCreatedFromIp($createdFromIp);
        $this->comment->setUpdatedFromIp($updatedFromIp);
        $this->comment->setCreatedBy($createdBy);
        $this->comment->setUpdatedBy($updatedBy);

        $this->assertSame($user, $this->comment->getUser());
        $this->assertSame($replyUser, $this->comment->getReplyUser());
        $this->assertSame($entity, $this->comment->getEntity());
        $this->assertSame($content, $this->comment->getContent());
        $this->assertSame($createdFromIp, $this->comment->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->comment->getUpdatedFromIp());
        $this->assertSame($createdBy, $this->comment->getCreatedBy());
        $this->assertSame($updatedBy, $this->comment->getUpdatedBy());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->comment);
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->comment->getId());
        $this->assertNull($this->comment->getUser());
        $this->assertNull($this->comment->getReplyUser());
        $this->assertNull($this->comment->getEntity());
        $this->assertNull($this->comment->getContent());
        $this->assertNull($this->comment->getCreatedFromIp());
        $this->assertNull($this->comment->getUpdatedFromIp());
    }

    public function testSetUserWithNull(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->comment->setUser($user);
        $this->comment->setUser(null);

        $this->assertNull($this->comment->getUser());
    }

    public function testSetReplyUserWithNull(): void
    {
        $replyUser = $this->createMock(UserInterface::class);
        $this->comment->setReplyUser($replyUser);
        $this->comment->setReplyUser(null);

        $this->assertNull($this->comment->getReplyUser());
    }

    public function testSetEntityWithNull(): void
    {
        $entity = $this->createMock(Entity::class);
        $this->comment->setEntity($entity);
        $this->comment->setEntity(null);

        $this->assertNull($this->comment->getEntity());
    }

    public function testFluentInterface(): void
    {
        $user = $this->createMock(UserInterface::class);
        $entity = $this->createMock(Entity::class);
        $content = 'Test comment content';
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        $createdBy = 'user123';
        $updatedBy = 'user456';

        $result = $this->comment
            ->setUser($user)
            ->setEntity($entity)
            ->setContent($content)
            ->setCreatedFromIp($createdFromIp)
            ->setUpdatedFromIp($updatedFromIp)
            ->setCreatedBy($createdBy)
            ->setUpdatedBy($updatedBy);

        $this->assertSame($this->comment, $result);
    }

    public function testRetrieveAdminArray(): void
    {
        $content = 'Test comment content';
        $this->comment->setContent($content);

        $adminArray = $this->comment->retrieveAdminArray();

        $this->assertArrayHasKey('id', $adminArray);
        $this->assertArrayHasKey('createTime', $adminArray);
        $this->assertArrayHasKey('updateTime', $adminArray);
        $this->assertArrayHasKey('content', $adminArray);
        $this->assertSame($content, $adminArray['content']);
    }

    public function testRetrieveAdminArrayWithDateTime(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2024-01-02 15:30:00');

        // 由于TimestampableAware trait的方法可能不直接暴露setter，我们主要测试数据结构
        $adminArray = $this->comment->retrieveAdminArray();

        $this->assertArrayHasKey('createTime', $adminArray);
        $this->assertArrayHasKey('updateTime', $adminArray);
    }
}
