<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Exception\ModelNotFoundException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(ModelNotFoundException::class)]
final class ModelNotFoundExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return ModelNotFoundException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return \RuntimeException::class;
    }
}
