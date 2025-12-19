<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Enum\TopicSort;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(TopicSort::class)]
final class TopicSortTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('id-desc', TopicSort::ID_DESC->value);
        $this->assertSame('id-asc', TopicSort::ID_ASC->value);
        $this->assertSame('random', TopicSort::RANDOM->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('ID倒序', TopicSort::ID_DESC->getLabel());
        $this->assertSame('ID顺序', TopicSort::ID_ASC->getLabel());
        $this->assertSame('随机返回', TopicSort::RANDOM->getLabel());
    }

    public function testToArray(): void
    {
        $enumCase = TopicSort::ID_DESC;
        $result = $enumCase->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('id-desc', $result['value']);
        $this->assertSame('ID倒序', $result['label']);

        // 测试另一个枚举值
        $enumCase2 = TopicSort::RANDOM;
        $result2 = $enumCase2->toArray();
        $this->assertSame('random', $result2['value']);
        $this->assertSame('随机返回', $result2['label']);
    }

    public function testGenOptions(): void
    {
        $options = TopicSort::genOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // 验证返回的是所有枚举值的选项
        $enumCases = TopicSort::cases();
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
