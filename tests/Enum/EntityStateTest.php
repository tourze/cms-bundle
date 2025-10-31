<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Enum;

use CmsBundle\Enum\EntityState;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(EntityState::class)]
final class EntityStateTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('draft', EntityState::DRAFT->value);
        $this->assertSame('published', EntityState::PUBLISHED->value);
        $this->assertSame('revoked', EntityState::REVOKED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('草稿', EntityState::DRAFT->getLabel());
        $this->assertSame('已发布', EntityState::PUBLISHED->getLabel());
        $this->assertSame('已撤回', EntityState::REVOKED->getLabel());
    }

    public function testToArray(): void
    {
        $enumCase = EntityState::DRAFT;
        $result = $enumCase->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('draft', $result['value']);
        $this->assertSame('草稿', $result['label']);

        // 测试另一个枚举值
        $enumCase2 = EntityState::PUBLISHED;
        $result2 = $enumCase2->toArray();
        $this->assertSame('published', $result2['value']);
        $this->assertSame('已发布', $result2['label']);
    }

    public function testGenOptions(): void
    {
        $options = EntityState::genOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // 验证返回的是所有枚举值的选项
        $enumCases = EntityState::cases();
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
