<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Tag;
use CmsBundle\Entity\TagGroup;
use PHPUnit\Framework\TestCase;

class TagGroupTest extends TestCase
{
    private TagGroup $tagGroup;

    protected function setUp(): void
    {
        $this->tagGroup = new TagGroup();
    }

    public function testGettersAndSetters(): void
    {
        $name = 'Test Group';

        $this->tagGroup->setName($name);

        $this->assertSame($name, $this->tagGroup->getName());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->tagGroup);

        // 测试有名称时，但ID为null时仍返回空字符串
        $this->tagGroup->setName('Test Group');
        $this->assertSame('', (string) $this->tagGroup);  // ID为null时返回空字符串
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->tagGroup->getId());
        $this->assertNull($this->tagGroup->getName());
        $this->assertCount(0, $this->tagGroup->getTags());
    }

    public function testAddTag(): void
    {
        $tag = $this->createMock(Tag::class);

        $tag->expects($this->once())
            ->method('setGroups')
            ->with($this->tagGroup);

        $result = $this->tagGroup->addTag($tag);

        $this->assertSame($this->tagGroup, $result);
        $this->assertTrue($this->tagGroup->getTags()->contains($tag));
    }

    public function testAddTagTwice(): void
    {
        $tag = $this->createMock(Tag::class);

        $tag->expects($this->once())
            ->method('setGroups')
            ->with($this->tagGroup);

        $this->tagGroup->addTag($tag);
        $this->tagGroup->addTag($tag); // 第二次添加应该被忽略

        $this->assertCount(1, $this->tagGroup->getTags());
    }

    public function testRemoveTag(): void
    {
        $tag = $this->createMock(Tag::class);

        // 先添加tag
        $tag->expects($this->once())
            ->method('setGroups')
            ->with($this->tagGroup);
        $this->tagGroup->addTag($tag);

        // 创建一个新的mock对象用于移除测试，避免期望冲突
        $tagForRemove = $this->createMock(Tag::class);
        $tagForRemove->expects($this->once())
            ->method('getGroups')
            ->willReturn($this->tagGroup);
        $tagForRemove->expects($this->once())
            ->method('setGroups')
            ->with(null);
        
        // 手动添加到集合中进行移除测试
        $this->tagGroup->getTags()->add($tagForRemove);

        $result = $this->tagGroup->removeTag($tagForRemove);

        $this->assertSame($this->tagGroup, $result);
        $this->assertFalse($this->tagGroup->getTags()->contains($tagForRemove));
    }

    public function testRemoveTagNotBelongingToGroup(): void
    {
        $tag = $this->createMock(Tag::class);

        $tag->expects($this->never())
            ->method('setGroups');

        $result = $this->tagGroup->removeTag($tag);

        $this->assertSame($this->tagGroup, $result);
    }

    public function testFluentInterface(): void
    {
        $name = 'Test Group';

        $result = $this->tagGroup->setName($name);

        $this->assertSame($this->tagGroup, $result);
    }
}
