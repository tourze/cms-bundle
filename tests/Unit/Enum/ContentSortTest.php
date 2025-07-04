<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Unit\Enum;

use CmsBundle\Enum\ContentSort;
use PHPUnit\Framework\TestCase;

class ContentSortTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('id-desc', ContentSort::ID_DESC->value);
        $this->assertSame('id-asc', ContentSort::ID_ASC->value);
        $this->assertSame('sort-number-desc', ContentSort::SORT_NUMBER_DESC->value);
        $this->assertSame('sort-number-asc', ContentSort::SORT_NUMBER_ASC->value);
        $this->assertSame('update-time-desc', ContentSort::UPDATE_TIME_DESC->value);
        $this->assertSame('update-time-asc', ContentSort::UPDATE_TIME_ASC->value);
        $this->assertSame('visit-count-desc', ContentSort::VISIT_COUNT_DESC->value);
        $this->assertSame('visit-count-asc', ContentSort::VISIT_COUNT_ASC->value);
        $this->assertSame('like-count-desc', ContentSort::LIKE_COUNT_DESC->value);
        $this->assertSame('like-count-asc', ContentSort::LIKE_COUNT_ASC->value);
        $this->assertSame('collect-count-desc', ContentSort::COLLECT_COUNT_DESC->value);
        $this->assertSame('collect-count-asc', ContentSort::COLLECT_COUNT_ASC->value);
        $this->assertSame('share-count-desc', ContentSort::SHARE_COUNT_DESC->value);
        $this->assertSame('share-count-asc', ContentSort::SHARE_COUNT_ASC->value);
        $this->assertSame('keyword-count-desc', ContentSort::KEYWORD_COUNT_DESC->value);
        $this->assertSame('keyword-count-asc', ContentSort::KEYWORD_COUNT_ASC->value);
        $this->assertSame('random', ContentSort::RANDOM->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('ID倒序', ContentSort::ID_DESC->getLabel());
        $this->assertSame('ID顺序', ContentSort::ID_ASC->getLabel());
        $this->assertSame('排序编号倒序', ContentSort::SORT_NUMBER_DESC->getLabel());
        $this->assertSame('排序编号顺序', ContentSort::SORT_NUMBER_ASC->getLabel());
        $this->assertSame('更新时间倒序', ContentSort::UPDATE_TIME_DESC->getLabel());
        $this->assertSame('更新时间顺序', ContentSort::UPDATE_TIME_ASC->getLabel());
        $this->assertSame('访问次数倒序', ContentSort::VISIT_COUNT_DESC->getLabel());
        $this->assertSame('访问次数顺序', ContentSort::VISIT_COUNT_ASC->getLabel());
        $this->assertSame('点赞次数倒序', ContentSort::LIKE_COUNT_DESC->getLabel());
        $this->assertSame('点赞次数顺序', ContentSort::LIKE_COUNT_ASC->getLabel());
        $this->assertSame('收藏次数倒序', ContentSort::COLLECT_COUNT_DESC->getLabel());
        $this->assertSame('收藏次数顺序', ContentSort::COLLECT_COUNT_ASC->getLabel());
        $this->assertSame('分享次数倒序', ContentSort::SHARE_COUNT_DESC->getLabel());
        $this->assertSame('分享次数顺序', ContentSort::SHARE_COUNT_ASC->getLabel());
        $this->assertSame('关键词命中倒序', ContentSort::KEYWORD_COUNT_DESC->getLabel());
        $this->assertSame('关键词命中顺序', ContentSort::KEYWORD_COUNT_ASC->getLabel());
        $this->assertSame('随机返回', ContentSort::RANDOM->getLabel());
    }
}