<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\SearchLog;
use PHPUnit\Framework\TestCase;

class SearchLogTest extends TestCase
{
    private SearchLog $searchLog;

    protected function setUp(): void
    {
        $this->searchLog = new SearchLog();
    }

    public function testGettersAndSetters(): void
    {
        $memberId = 123;
        $keyword = 'test keyword';
        $categoryId = 456;
        $topicId = 789;
        $count = 10;
        $hit = 5;

        $this->searchLog->setMemberId($memberId);
        $this->searchLog->setKeyword($keyword);
        $this->searchLog->setCategoryId($categoryId);
        $this->searchLog->setTopicId($topicId);
        $this->searchLog->setCount($count);
        $this->searchLog->setHit($hit);

        $this->assertSame($memberId, $this->searchLog->getMemberId());
        $this->assertSame($keyword, $this->searchLog->getKeyword());
        $this->assertSame($categoryId, $this->searchLog->getCategoryId());
        $this->assertSame($topicId, $this->searchLog->getTopicId());
        $this->assertSame($count, $this->searchLog->getCount());
        $this->assertSame($hit, $this->searchLog->getHit());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->searchLog);

        // 测试有关键词时的字符串表示（ID为null时返回空字符串）
        $this->searchLog->setKeyword('test keyword');
        $result = (string) $this->searchLog;
        $this->assertSame('', $result);  // ID为null时返回空字符串
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->searchLog->getId());
        $this->assertSame(0, $this->searchLog->getCategoryId());
        $this->assertSame(0, $this->searchLog->getTopicId());
        $this->assertSame(1, $this->searchLog->getCount());
        $this->assertSame(0, $this->searchLog->getHit());
    }

    public function testRetrieveApiArray(): void
    {
        $keyword = 'test keyword';
        $count = 10;

        $this->searchLog->setKeyword($keyword);
        $this->searchLog->setCount($count);

        $apiArray = $this->searchLog->retrieveApiArray();

        $this->assertArrayHasKey('id', $apiArray);
        $this->assertArrayHasKey('keyword', $apiArray);
        $this->assertArrayHasKey('count', $apiArray);
        $this->assertSame($keyword, $apiArray['keyword']);
        $this->assertSame($count, $apiArray['count']);
    }

    public function testDefaultValues(): void
    {
        // 测试默认值
        $this->assertSame(0, $this->searchLog->getCategoryId());
        $this->assertSame(0, $this->searchLog->getTopicId());
        $this->assertSame(1, $this->searchLog->getCount());
        $this->assertSame(0, $this->searchLog->getHit());
    }

    public function testZeroValues(): void
    {
        // 测试设置为0的值
        $this->searchLog->setMemberId(0);
        $this->searchLog->setCategoryId(0);
        $this->searchLog->setTopicId(0);
        $this->searchLog->setCount(0);
        $this->searchLog->setHit(0);

        $this->assertSame(0, $this->searchLog->getMemberId());
        $this->assertSame(0, $this->searchLog->getCategoryId());
        $this->assertSame(0, $this->searchLog->getTopicId());
        $this->assertSame(0, $this->searchLog->getCount());
        $this->assertSame(0, $this->searchLog->getHit());
    }
}
