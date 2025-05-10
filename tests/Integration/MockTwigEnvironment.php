<?php

namespace CmsBundle\Tests\Integration;

use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\ArrayLoader;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * 模拟Twig环境
 */
class MockTwigEnvironment extends Environment
{
    public function __construct()
    {
        parent::__construct(new ArrayLoader([]));
    }
    
    /**
     * 渲染模板
     */
    public function render($name, array $context = []): string
    {
        return json_encode($context, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 渲染模板
     */
    public function renderTemplate($template, array $context = []): string
    {
        return json_encode($context, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 添加全局变量
     */
    public function addGlobal($name, $value): void
    {
        // 不做任何事情
    }
    
    /**
     * 添加扩展
     */
    public function addExtension(ExtensionInterface $extension): void
    {
        // 不做任何事情
    }
    
    /**
     * 添加过滤器
     */
    public function addFilter(TwigFilter $filter): void
    {
        // 不做任何事情
    }
    
    /**
     * 添加函数
     */
    public function addFunction(TwigFunction $function): void
    {
        // 不做任何事情
    }
    
    /**
     * 添加测试
     */
    public function addTest(TwigTest $test): void
    {
        // 不做任何事情
    }
    
    /**
     * 添加令牌解析器
     */
    public function addTokenParser(TokenParserInterface $parser): void
    {
        // 不做任何事情
    }
    
    /**
     * 添加节点访问器
     */
    public function addNodeVisitor(NodeVisitorInterface $visitor): void
    {
        // 不做任何事情
    }
} 