<?php

namespace CmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum EntityState: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case REVOKED = 'revoked';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => '草稿',
            self::PUBLISHED => '已发布',
            self::REVOKED => '已撤回',
        };
    }
}
