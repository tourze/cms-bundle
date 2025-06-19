<?php

namespace CmsBundle\Enum;

use Carbon\CarbonImmutable;
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
            self::DATE_TIME => '日期+时间',
            self::DECIMAL => '小数',
            self::INTEGER => '整数',
            self::STRING => '字符串',
            self::TEXT => '长文本',
            self::RICH_TEXT => '富文本',
            self::SINGLE_IMAGE => '单图',
            self::MULTIPLE_IMAGE => '多图',
            self::SINGLE_FILE => '单文件',
            self::SINGLE_SELECT => '下拉单选',
            self::MULTIPLE_SELECT => '下拉多选',
            self::TAGS_SELECT => '下拉多选(可自由添加)',
            self::FORMULA => '公式',
        };
    }

    public static function getStorableValue(Attribute $attribute, mixed $data): string
    {
        switch ($attribute->getType()) {
            case self::DATE:
                if (is_integer($data)) {
                    $data = CarbonImmutable::createFromTimestamp($data, date_default_timezone_get())->format('Y-m-d');
                }

                break;
            case self::DATE_TIME:
                if (is_integer($data)) {
                    $data = CarbonImmutable::createFromTimestamp($data, date_default_timezone_get())->format('Y-m-d H:i:s');
                }

                break;
            case self::SINGLE_FILE:
            case self::SINGLE_IMAGE:
            case self::MULTIPLE_IMAGE:
                $data = json_encode($data);
                break;
            case self::DECIMAL:
            case self::INTEGER:
                $data = (string) $data;
                break;
            case self::MULTIPLE_SELECT:
            case self::TAGS_SELECT:
                if (is_array($data)) {
                    $data = implode(',', $data);
                }
        }

        return (string) $data;
    }
}
