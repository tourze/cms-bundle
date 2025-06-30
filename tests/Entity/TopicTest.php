<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Topic;
use PHPUnit\Framework\TestCase;

class TopicTest extends TestCase
{
    private Topic $topic;

    protected function setUp(): void
    {
        $this->topic = new Topic();
    }

    public function testGettersAndSetters(): void
    {
        $title = 'Test Topic';
        $description = 'Test Description';
        $thumb = 'thumb.jpg';
        $banners = ['banner1' => 'banner1.jpg', 'banner2' => 'banner2.jpg'];
        $recommend = true;
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->topic->setTitle($title);
        $this->topic->setDescription($description);
        $this->topic->setThumb($thumb);
        $this->topic->setBanners($banners);
        $this->topic->setRecommend($recommend);
        $this->topic->setCreatedFromIp($createdFromIp);
        $this->topic->setUpdatedFromIp($updatedFromIp);

        $this->assertSame($title, $this->topic->getTitle());
        $this->assertSame($description, $this->topic->getDescription());
        $this->assertSame($thumb, $this->topic->getThumb());
        $this->assertSame($banners, $this->topic->getBanners());
        $this->assertSame($recommend, $this->topic->getRecommend());
        $this->assertSame($createdFromIp, $this->topic->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->topic->getUpdatedFromIp());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->topic);

        // 测试有标题时，但ID为0时仍返回空字符串
        $this->topic->setTitle('Test Topic');
        $this->assertSame('', (string) $this->topic);  // ID为0时返回空字符串
    }

    public function testInitialValues(): void
    {
        $this->assertSame(0, $this->topic->getId());
        $this->assertNull($this->topic->getTitle());
        $this->assertNull($this->topic->getDescription());
        $this->assertNull($this->topic->getThumb());
        $this->assertSame([], $this->topic->getBanners());
        $this->assertNull($this->topic->getRecommend());
        $this->assertNull($this->topic->getCreatedFromIp());
        $this->assertNull($this->topic->getUpdatedFromIp());
        $this->assertCount(0, $this->topic->getEntities());
    }

    public function testAddEntity(): void
    {
        $entity = $this->createMock(Entity::class);

        $result = $this->topic->addEntity($entity);

        $this->assertSame($this->topic, $result);
        $this->assertTrue($this->topic->getEntities()->contains($entity));
        $this->assertSame(1, $this->topic->getEntityCount());
    }

    public function testAddEntityTwice(): void
    {
        $entity = $this->createMock(Entity::class);

        $this->topic->addEntity($entity);
        $this->topic->addEntity($entity); // 第二次添加应该被忽略

        $this->assertCount(1, $this->topic->getEntities());
        $this->assertSame(1, $this->topic->getEntityCount());
    }

    public function testRemoveEntity(): void
    {
        $entity = $this->createMock(Entity::class);

        $this->topic->addEntity($entity);
        $this->assertTrue($this->topic->getEntities()->contains($entity));

        $result = $this->topic->removeEntity($entity);

        $this->assertSame($this->topic, $result);
        $this->assertFalse($this->topic->getEntities()->contains($entity));
        $this->assertSame(0, $this->topic->getEntityCount());
    }

    public function testRemoveEntityNotInCollection(): void
    {
        $entity = $this->createMock(Entity::class);

        $result = $this->topic->removeEntity($entity);

        $this->assertSame($this->topic, $result);
        $this->assertSame(0, $this->topic->getEntityCount());
    }

    public function testFluentInterface(): void
    {
        $title = 'Test Topic';
        $description = 'Test Description';
        $thumb = 'thumb.jpg';
        $banners = ['banner' => 'banner.jpg'];
        $recommend = true;
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $result = $this->topic
            ->setTitle($title)
            ->setDescription($description)
            ->setThumb($thumb)
            ->setBanners($banners)
            ->setRecommend($recommend)
            ->setCreatedFromIp($createdFromIp)
            ->setUpdatedFromIp($updatedFromIp);

        $this->assertSame($this->topic, $result);
    }

    public function testSetDescriptionWithNull(): void
    {
        $this->topic->setDescription('test');
        $this->topic->setDescription(null);

        $this->assertNull($this->topic->getDescription());
    }

    public function testSetThumbWithNull(): void
    {
        $this->topic->setThumb('thumb.jpg');
        $this->topic->setThumb(null);

        $this->assertNull($this->topic->getThumb());
    }

    public function testSetBannersWithNull(): void
    {
        $this->topic->setBanners(['banner' => 'banner.jpg']);
        $this->topic->setBanners(null);

        $this->assertSame([], $this->topic->getBanners());
    }
}
