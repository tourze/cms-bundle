<?php

declare(strict_types=1);

namespace CmsBundle\Enum;

use CmsBundle\Entity\Attribute;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum FieldType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case DATE = 'date';
    case DATE_TIME = 'date-time';
    case DECIMAL = 'decimal';
    case INTEGER = 'integer';
    case STRING = 'string';
    case TEXT = 'text';
    case RICH_TEXT = 'rich-text';
    case SINGLE_IMAGE = 'single-image';
    case MULTIPLE_IMAGE = 'multiple-image';
    case SINGLE_FILE = 'single-file';
    case SINGLE_SELECT = 'single-select';
    case MULTIPLE_SELECT = 'multiple-select';
    case TAGS_SELECT = 'tags-select';
    case FORMULA = 'formula';

    public function getLabel(): string
    {
        return match ($this) {
            self::DATE => '日期',
            self::DATE_TIME => '日期时间',
            self::DECIMAL => '小数',
            self::INTEGER => '整数',
            self::STRING => '字符串',
            self::TEXT => '文本',
            self::RICH_TEXT => '富文本',
            self::SINGLE_IMAGE => '单个图片',
            self::MULTIPLE_IMAGE => '多个图片',
            self::SINGLE_FILE => '单个文件',
            self::SINGLE_SELECT => '单选',
            self::MULTIPLE_SELECT => '多选',
            self::TAGS_SELECT => '标签选择',
            self::FORMULA => '公式',
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }

    /**
     * 获取可存储的值
     */
    public static function getStorableValue(Attribute $attribute, mixed $value): mixed
    {
        $type = $attribute->getType();

        return match ($type) {
            self::STRING, self::TEXT, self::RICH_TEXT => \is_scalar($value) || null === $value ? (string) $value : '',
            self::INTEGER => is_numeric($value) ? (int) $value : 0,
            self::DECIMAL => \is_scalar($value) || null === $value ? (string) $value : '0',
            self::DATE => $value instanceof \DateTimeInterface
                ? $value->format('Y-m-d')
                : self::convertToDateString($value),
            self::DATE_TIME => $value instanceof \DateTimeInterface
                ? $value->format('Y-m-d H:i:s')
                : self::convertToDateTimeString($value),
            default => $value,
        };
    }

    /**
     * 转换值为日期字符串.
     */
    private static function convertToDateString(mixed $value): string
    {
        if (is_numeric($value)) {
            return date('Y-m-d', (int) $value);
        }

        $timestamp = strtotime(\is_scalar($value) || null === $value ? (string) $value : '');

        return false !== $timestamp ? date('Y-m-d', $timestamp) : date('Y-m-d');
    }

    /**
     * 转换值为日期时间字符串.
     */
    private static function convertToDateTimeString(mixed $value): string
    {
        if (is_numeric($value)) {
            return date('Y-m-d H:i:s', (int) $value);
        }

        $timestamp = strtotime(\is_scalar($value) || null === $value ? (string) $value : '');

        return false !== $timestamp ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d H:i:s');
    }
}
