<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Unit\Enum;

use CmsBundle\Entity\Attribute;
use CmsBundle\Enum\FieldType;
use PHPUnit\Framework\TestCase;

class FieldTypeTest extends TestCase
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
        $this->assertSame('日期+时间', FieldType::DATE_TIME->getLabel());
        $this->assertSame('小数', FieldType::DECIMAL->getLabel());
        $this->assertSame('整数', FieldType::INTEGER->getLabel());
        $this->assertSame('字符串', FieldType::STRING->getLabel());
        $this->assertSame('长文本', FieldType::TEXT->getLabel());
        $this->assertSame('富文本', FieldType::RICH_TEXT->getLabel());
        $this->assertSame('单图', FieldType::SINGLE_IMAGE->getLabel());
        $this->assertSame('多图', FieldType::MULTIPLE_IMAGE->getLabel());
        $this->assertSame('单文件', FieldType::SINGLE_FILE->getLabel());
        $this->assertSame('下拉单选', FieldType::SINGLE_SELECT->getLabel());
        $this->assertSame('下拉多选', FieldType::MULTIPLE_SELECT->getLabel());
        $this->assertSame('下拉多选(可自由添加)', FieldType::TAGS_SELECT->getLabel());
        $this->assertSame('公式', FieldType::FORMULA->getLabel());
    }

    public function testGetStorableValueWithStringData(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::STRING);

        $result = FieldType::getStorableValue($attribute, 'test string');
        $this->assertSame('test string', $result);
    }

    public function testGetStorableValueWithIntegerData(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::INTEGER);

        $result = FieldType::getStorableValue($attribute, 123);
        $this->assertSame('123', $result);
    }

    public function testGetStorableValueWithDecimalData(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::DECIMAL);

        $result = FieldType::getStorableValue($attribute, 123.45);
        $this->assertSame('123.45', $result);
    }

    public function testGetStorableValueWithDateFromTimestamp(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::DATE);

        $timestamp = strtotime('2024-01-15 14:30:00');
        $result = FieldType::getStorableValue($attribute, $timestamp);
        $this->assertSame('2024-01-15', $result);
    }

    public function testGetStorableValueWithDateTimeFromTimestamp(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::DATE_TIME);

        $timestamp = strtotime('2024-01-15 14:30:00');
        $result = FieldType::getStorableValue($attribute, $timestamp);
        $this->assertSame('2024-01-15 14:30:00', $result);
    }

    public function testGetStorableValueWithArrayForMultipleSelect(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::MULTIPLE_SELECT);

        $result = FieldType::getStorableValue($attribute, ['option1', 'option2', 'option3']);
        $this->assertSame('option1,option2,option3', $result);
    }

    public function testGetStorableValueWithArrayForTagsSelect(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::TAGS_SELECT);

        $result = FieldType::getStorableValue($attribute, ['tag1', 'tag2']);
        $this->assertSame('tag1,tag2', $result);
    }

    public function testGetStorableValueWithArrayForSingleImage(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::SINGLE_IMAGE);

        $imageData = ['url' => 'http://example.com/image.jpg', 'alt' => 'test image'];
        $result = FieldType::getStorableValue($attribute, $imageData);
        $this->assertSame(json_encode($imageData), $result);
    }
}