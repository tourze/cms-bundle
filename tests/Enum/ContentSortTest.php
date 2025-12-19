<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Enum\ContentSort;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ContentSort::class)]
final class ContentSortTest extends AbstractEnumTestCase
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

    public function testToArray(): void
    {
        $enumCase = ContentSort::ID_DESC;
        $result = $enumCase->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('id-desc', $result['value']);
        $this->assertSame('ID倒序', $result['label']);

        // 测试另一个枚举值
        $enumCase2 = ContentSort::RANDOM;
        $result2 = $enumCase2->toArray();
        $this->assertSame('random', $result2['value']);
        $this->assertSame('随机返回', $result2['label']);
    }

    public function testGenOptions(): void
    {
        $options = ContentSort::genOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // 验证返回的是所有枚举值的选项
        $enumCases = ContentSort::cases();
        $this->assertCount(\count($enumCases), $options);

        // 验证每个选项的结构
        foreach ($options as $option) {
            $this->assertIsArray($option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
        }
    }
}
