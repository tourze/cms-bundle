<?php

namespace CmsBundle\Tests\Integration;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * 模拟Twig环境
 */
class MockTwigEnvironment extends Environment
{
    public function __construct()
    {
        // 使用空的数组加载器，禁用严格变量检查
        parent::__construct(new ArrayLoader([]), [
            'strict_variables' => false,
            'debug' => true,
        ]);
    }
} 