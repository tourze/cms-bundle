<?php

namespace CmsBundle\Service;

use CmsBundle\Entity\RenderTemplate;
use CmsBundle\Repository\RenderTemplateRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Attribute\AsRoutingConditionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tourze\DoctrineHelper\CacheHelper;

/**
 * 决定是否可以激活路由
 */
#[AsRoutingConditionService(alias: 'cms_routing_condition')]
class RoutingCondition implements RoutingConditionInterface
{
    final public const TEMPLATE_KEY = '_cmsRenderTemplateId';

    public function __construct(
        private readonly RenderTemplateRepository $templateRepository,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
    ) {
    }

    public function check(RequestContext $context, Request $request): bool
    {
        $routes = $this->cache->get('cms-template-routes', function (ItemInterface $item) {
            $routes = $this->getRoutes();
            $item->set($routes);
            $item->tag(CacheHelper::getClassTags(RenderTemplate::class));
            $item->expiresAfter(60 * 60 * 24);

            return $routes;
        });
        if (!$routes) {
            return false;
        }

        $matcher = new UrlMatcher($routes, $context);

        $path = $this->findRealPath($request);
        $parameters = $matcher->match($path);

        if (empty($parameters)) {
            return false;
        }

        foreach ($parameters as $k => $v) {
            if ($request->attributes->has($k)) {
                continue;
            }

            $request->attributes->set($k, $v);
        }

        return true;
    }

    private function getRoutes(): ?RouteCollection
    {
        $routes = new RouteCollection();

        try {
            $templates = $this->templateRepository->findBy(['valid' => true]);
        } catch (\Throwable $exception) {
            $this->logger->error('检查CMS路由配置时发生异常', [
                'exception' => $exception,
            ]);

            return null;
        }

        foreach ($templates as $renderTemplate) {
            $route = new Route($renderTemplate->getPath(), [self::TEMPLATE_KEY => $renderTemplate->getId()]);
            $routes->add("{$renderTemplate->getId()}-{$renderTemplate->getTitle()}", $route);
        }

        return $routes;
    }

    /**
     * 格式化路径，路径必然是 / 开头的
     */
    private function findRealPath(Request $request): string
    {
        $path = $request->getPathInfo();
        $path = '/' . trim($path, '/');

        return str_replace('//', '', $path);
    }
}
