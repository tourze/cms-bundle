<?php

declare(strict_types=1);

namespace CmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 内容列表排序枚举.
 */
enum ContentSort: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ID_DESC = 'id-desc';
    case ID_ASC = 'id-asc';
    case SORT_NUMBER_DESC = 'sort-number-desc';
    case SORT_NUMBER_ASC = 'sort-number-asc';
    case UPDATE_TIME_DESC = 'update-time-desc';
    case UPDATE_TIME_ASC = 'update-time-asc';
    case VISIT_COUNT_DESC = 'visit-count-desc';
    case VISIT_COUNT_ASC = 'visit-count-asc';
    case LIKE_COUNT_DESC = 'like-count-desc';
    case LIKE_COUNT_ASC = 'like-count-asc';
    case COLLECT_COUNT_DESC = 'collect-count-desc';
    case COLLECT_COUNT_ASC = 'collect-count-asc';
    case SHARE_COUNT_DESC = 'share-count-desc';
    case SHARE_COUNT_ASC = 'share-count-asc';
    case KEYWORD_COUNT_DESC = 'keyword-count-desc';
    case KEYWORD_COUNT_ASC = 'keyword-count-asc';
    case RANDOM = 'random';

    public function getLabel(): string
    {
        return match ($this) {
            self::ID_DESC => 'ID倒序',
            self::ID_ASC => 'ID顺序',

            self::SORT_NUMBER_DESC => '排序编号倒序',
            self::SORT_NUMBER_ASC => '排序编号顺序',

            self::UPDATE_TIME_DESC => '更新时间倒序',
            self::UPDATE_TIME_ASC => '更新时间顺序',

            self::VISIT_COUNT_DESC => '访问次数倒序',
            self::VISIT_COUNT_ASC => '访问次数顺序',

            self::LIKE_COUNT_DESC => '点赞次数倒序',
            self::LIKE_COUNT_ASC => '点赞次数顺序',

            self::COLLECT_COUNT_DESC => '收藏次数倒序',
            self::COLLECT_COUNT_ASC => '收藏次数顺序',

            self::SHARE_COUNT_DESC => '分享次数倒序',
            self::SHARE_COUNT_ASC => '分享次数顺序',

            self::KEYWORD_COUNT_DESC => '关键词命中倒序',
            self::KEYWORD_COUNT_ASC => '关键词命中顺序',

            self::RANDOM => '随机返回',
        };
    }
}
