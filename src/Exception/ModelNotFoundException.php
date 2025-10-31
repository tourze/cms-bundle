<?php

declare(strict_types=1);

namespace CmsBundle\Exception;

/**
 * 当CMS模型未找到时抛出的异常.
 */
class ModelNotFoundException extends \RuntimeException
{
    public function __construct(string $modelCode, ?\Throwable $previous = null)
    {
        parent::__construct("找不到指定CMS模型: {$modelCode}", 0, $previous);
    }
}
