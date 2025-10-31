<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Enum;

use CmsBundle\Entity\Attribute;
use CmsBundle\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(FieldType::class)]
final class FieldTypeTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('date', FieldType::DATE->value);
        $this->assertSame('date-time', FieldType::DATE_TIME->value);
        $this->assertSame('decimal', FieldType::DECIMAL->value);
        $this->assertSame('integer', FieldType::INTEGER->value);
        $this->assertSame('string', FieldType::STRING->value);
        $this->assertSame('text', FieldType::TEXT->value);
        $this->assertSame('rich-text', FieldType::RICH_TEXT->value);
        $this->assertSame('single-image', FieldType::SINGLE_IMAGE->value);
        $this->assertSame('multiple-image', FieldType::MULTIPLE_IMAGE->value);
        $this->assertSame('single-file', FieldType::SINGLE_FILE->value);
        $this->assertSame('single-select', FieldType::SINGLE_SELECT->value);
        $this->assertSame('multiple-select', FieldType::MULTIPLE_SELECT->value);
        $this->assertSame('tags-select', FieldType::TAGS_SELECT->value);
        $this->assertSame('formula', FieldType::FORMULA->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('日期', FieldType::DATE->getLabel());
        $this->assertSame('日期时间', FieldType::DATE_TIME->getLabel());
        $this->assertSame('小数', FieldType::DECIMAL->getLabel());
        $this->assertSame('整数', FieldType::INTEGER->getLabel());
        $this->assertSame('字符串', FieldType::STRING->getLabel());
        $this->assertSame('文本', FieldType::TEXT->getLabel());
        $this->assertSame('富文本', FieldType::RICH_TEXT->getLabel());
        $this->assertSame('单个图片', FieldType::SINGLE_IMAGE->getLabel());
        $this->assertSame('多个图片', FieldType::MULTIPLE_IMAGE->getLabel());
        $this->assertSame('单个文件', FieldType::SINGLE_FILE->getLabel());
        $this->assertSame('单选', FieldType::SINGLE_SELECT->getLabel());
        $this->assertSame('多选', FieldType::MULTIPLE_SELECT->getLabel());
        $this->assertSame('标签选择', FieldType::TAGS_SELECT->getLabel());
        $this->assertSame('公式', FieldType::FORMULA->getLabel());
    }

    public function testGetStorableValueWithStringData(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::STRING;
            }
        };

        $result = FieldType::getStorableValue($attribute, 'test string');
        $this->assertSame('test string', $result);
    }

    public function testGetStorableValueWithIntegerData(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::INTEGER;
            }
        };

        $result = FieldType::getStorableValue($attribute, 123);
        $this->assertSame(123, $result);
    }

    public function testGetStorableValueWithDecimalData(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::DECIMAL;
            }
        };

        $result = FieldType::getStorableValue($attribute, 123.45);
        $this->assertSame('123.45', $result);
    }

    public function testGetStorableValueWithDateFromTimestamp(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::DATE;
            }
        };

        $timestamp = strtotime('2024-01-15 14:30:00');
        $result = FieldType::getStorableValue($attribute, $timestamp);
        $this->assertSame('2024-01-15', $result);
    }

    public function testGetStorableValueWithDateTimeFromTimestamp(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::DATE_TIME;
            }
        };

        $timestamp = strtotime('2024-01-15 14:30:00');
        $result = FieldType::getStorableValue($attribute, $timestamp);
        $this->assertSame('2024-01-15 14:30:00', $result);
    }

    public function testGetStorableValueWithArrayForMultipleSelect(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::MULTIPLE_SELECT;
            }
        };

        $result = FieldType::getStorableValue($attribute, ['option1', 'option2', 'option3']);
        $this->assertSame(['option1', 'option2', 'option3'], $result);
    }

    public function testGetStorableValueWithArrayForTagsSelect(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::TAGS_SELECT;
            }
        };

        $result = FieldType::getStorableValue($attribute, ['tag1', 'tag2']);
        $this->assertSame(['tag1', 'tag2'], $result);
    }

    public function testGetStorableValueWithArrayForSingleImage(): void
    {
        /*
         * 使用 Attribute 匿名类实现的原因：
         * 1. Entity 是 Doctrine 实体类，不适合定义接口
         * 2. 在测试中需要模拟实体对象的属性和方法
         * 3. 匿名类实现更符合静态分析要求，避免 Mock 的类型推断问题
         */
        $attribute = new class extends Attribute {
            public function getType(): FieldType
            {
                return FieldType::SINGLE_IMAGE;
            }
        };

        $imageData = ['url' => 'http://example.com/image.jpg', 'alt' => 'test image'];
        $result = FieldType::getStorableValue($attribute, $imageData);
        $this->assertSame($imageData, $result);
    }

    public function testToArray(): void
    {
        $enumCase = FieldType::STRING;
        $result = $enumCase->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('string', $result['value']);
        $this->assertSame('字符串', $result['label']);

        // 测试另一个枚举值
        $enumCase2 = FieldType::RICH_TEXT;
        $result2 = $enumCase2->toArray();
        $this->assertSame('rich-text', $result2['value']);
        $this->assertSame('富文本', $result2['label']);
    }

    public function testGenOptions(): void
    {
        $options = FieldType::genOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // 验证返回的是所有枚举值的选项
        $enumCases = FieldType::cases();
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
