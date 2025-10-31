<?php

declare(strict_types=1);

namespace CmsBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\AutoJsControlBundle\AutoJsControlBundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\CatalogBundle\CatalogBundle;
use Tourze\CmsCollectBundle\CmsCollectBundle;
use Tourze\CmsLikeBundle\CmsLikeBundle;
use Tourze\DoctrineAsyncInsertBundle\DoctrineAsyncInsertBundle;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineTrackBundle\DoctrineTrackBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\JsonRPCLogBundle\JsonRPCLogBundle;
use Tourze\JsonRPCPaginatorBundle\JsonRPCPaginatorBundle;
use Tourze\LockServiceBundle\LockServiceBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use Tourze\TagManageBundle\TagManageBundle;

class CmsBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            AutoJsControlBundle::class => ['all' => true],
            CatalogBundle::class => ['all' => true],
            TagManageBundle::class => ['all' => true],
            CmsCollectBundle::class => ['all' => true],
            CmsLikeBundle::class => ['all' => true],
            DoctrineAsyncInsertBundle::class => ['all' => true],
            DoctrineIndexedBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineTrackBundle::class => ['all' => true],
            DoctrineUserBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            KnpPaginatorBundle::class => ['all' => true],
            JsonRPCPaginatorBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            JsonRPCLogBundle::class => ['all' => true],
            LockServiceBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
        ];
    }
}
