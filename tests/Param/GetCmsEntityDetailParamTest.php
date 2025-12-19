<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\CmsBundle\Param\GetCmsEntityDetailParam;

/**
 * @internal
 */
#[CoversClass(GetCmsEntityDetailParam::class)]
final class GetCmsEntityDetailParamTest extends TestCase
{
    public function testConstruction(): void
    {
        $param = new GetCmsEntityDetailParam(entityId: 123);

        $this->assertSame(123, $param->entityId);
    }

    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetCmsEntityDetailParam(entityId: 1);

        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Contracts\RpcParamInterface::class, $param);
    }
}
