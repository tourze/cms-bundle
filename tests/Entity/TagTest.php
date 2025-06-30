<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Tag;
use CmsBundle\Entity\TagGroup;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    private Tag $tag;

    protected function setUp(): void
    {
        $this->tag = new Tag();
    }

    public function testGettersAndSetters(): void
    {
        $name = 'Test Tag';
        $group = $this->createMock(TagGroup::class);
        $valid = true;
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->tag->setName($name);
        $this->tag->setGroups($group);
        $this->tag->setValid($valid);
        $this->tag->setCreatedFromIp($createdFromIp);
        $this->tag->setUpdatedFromIp($updatedFromIp);

        $this->assertSame($name, $this->tag->getName());
        $this->assertSame($group, $this->tag->getGroups());
        $this->assertSame($valid, $this->tag->isValid());
        $this->assertSame($createdFromIp, $this->tag->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->tag->getUpdatedFromIp());
    }

    public function testStringable(): void
    {
        $this->assertSame('', (string) $this->tag);

        $this->tag->setName('Test Tag');
        $this->assertSame('', (string) $this->tag);  // ID为null时返回空字符串
    }

    public function testInitialValues(): void
    {
        $this->assertSame(0, $this->tag->getId());
        $this->assertNull($this->tag->getName());
        $this->assertNull($this->tag->getGroups());
        $this->assertFalse($this->tag->isValid());
        $this->assertCount(0, $this->tag->getEntities());
    }

    public function testAddEntity(): void
    {
        $entity = $this->createMock(Entity::class);

        $result = $this->tag->addEntity($entity);

        $this->assertSame($this->tag, $result);
        $this->assertTrue($this->tag->getEntities()->contains($entity));
        $this->assertSame(1, $this->tag->renderEntityCount());
    }

    public function testRemoveEntity(): void
    {
        $entity = $this->createMock(Entity::class);

        $this->tag->addEntity($entity);
        $result = $this->tag->removeEntity($entity);

        $this->assertSame($this->tag, $result);
        $this->assertFalse($this->tag->getEntities()->contains($entity));
        $this->assertSame(0, $this->tag->renderEntityCount());
    }

    public function testFluentInterface(): void
    {
        $name = 'Test Tag';
        $group = $this->createMock(TagGroup::class);
        $valid = true;

        $result = $this->tag
            ->setName($name)
            ->setGroups($group)
            ->setValid($valid);

        $this->assertSame($this->tag, $result);
    }
}
