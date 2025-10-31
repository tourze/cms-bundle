<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Exception;

use CmsBundle\Exception\ModelNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
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
