<?php

namespace CmsBundle\Tests\Exception;

use CmsBundle\Exception\ModelNotFoundException;
use PHPUnit\Framework\TestCase;

class ModelNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $modelCode = 'test-model';
        $exception = new ModelNotFoundException($modelCode);

        $this->assertSame("找不到指定CMS模型: {$modelCode}", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $modelCode = 'test-model';
        $previous = new \RuntimeException('Previous exception');
        $exception = new ModelNotFoundException($modelCode, $previous);

        $this->assertSame("找不到指定CMS模型: {$modelCode}", $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
