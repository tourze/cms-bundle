<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetCmsEntityDetailParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '文章ID')]
        public int $entityId,
    ) {
    }
}
