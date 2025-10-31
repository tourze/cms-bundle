<?php

declare(strict_types=1);

namespace CmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 专题列表排序.
 */
enum TopicSort: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ID_DESC = 'id-desc';
    case ID_ASC = 'id-asc';
    case RANDOM = 'random';

    public function getLabel(): string
    {
        return match ($this) {
            self::ID_DESC => 'ID倒序',
            self::ID_ASC => 'ID顺序',

            self::RANDOM => '随机返回',
        };
    }
}
