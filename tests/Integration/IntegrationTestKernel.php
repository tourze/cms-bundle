<?php

namespace CmsBundle\Tests\Integration;

use CmsBundle\AdminMenu;
use CmsBundle\CmsBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\JsonRPCCacheBundle\JsonRPCCacheBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;
use Tourze\JsonRPCSecurityBundle\Service\GrantService;
use Tourze\LockServiceBundle\LockServiceBundle;
use Tourze\LockServiceBundle\Service\LockService;
use Twig\Environment;

/**
 * 集成测试内核 - 确保所有必需服务都被注册
 */
class IntegrationTestKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DoctrineBundle();
        yield new SecurityBundle();
        yield new CmsBundle();
        yield new JsonRPCLockBundle();
        yield new JsonRPCSecurityBundle();
        yield new JsonRPCCacheBundle();
        yield new LockServiceBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        // 基本框架配置
        $container->extension('framework', [
            'secret' => 'TEST_SECRET',
            'test' => true,
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'router' => [
                'utf8' => true,
            ],
            'php_errors' => [
                'log' => true,
            ],
            'validation' => [
                'email_validation_mode' => 'html5',
            ],
            'uid' => [
                'default_uuid_version' => 7,
                'time_based_uuid_version' => 7,
            ],
        ]);

        // Doctrine 配置 - 使用SQLite数据库
        $container->extension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => '%kernel.cache_dir%/test.db',
                'memory' => false,
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'enable_lazy_ghost_objects' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping' => true,
                'resolve_target_entities' => [
                    'Symfony\Component\Security\Core\User\UserInterface' => 'CmsBundle\Tests\Integration\MockUser',
                ],
                'mappings' => [
                    'CmsBundle' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => dirname(__DIR__, 2) . '/src/Entity',
                        'prefix' => 'CmsBundle\Entity',
                        'alias' => 'CmsBundle',
                    ],
                    'CmsBundleTest' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => dirname(__DIR__, 1) . '/Integration',
                        'prefix' => 'CmsBundle\Tests\Integration',
                        'alias' => 'CmsBundleTest',
                    ],
                ],
            ],
        ]);
        
        // 安全配置
        $container->extension('security', [
            'password_hashers' => [
                'Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface' => 'auto',
            ],
            'providers' => [
                'users_in_memory' => ['memory' => null],
            ],
            'firewalls' => [
                'main' => [
                    'lazy' => true,
                    'provider' => 'users_in_memory',
                ],
            ],
        ]);
        
        // 服务配置
        $services = $container->services();
        
        // 注册模拟服务
        $services->set(LoggerInterface::class, MockLogger::class)
            ->public();
            
        $services->set(PaginatorInterface::class, MockPaginator::class)
            ->public();
            
        $services->set(CacheInterface::class, MockSimpleCache::class)
            ->public();
            
        $services->set(EventDispatcherInterface::class, MockEventDispatcher::class)
            ->public();
            
        $services->set(EventDispatcher::class, MockEventDispatcher::class)
            ->public();
            
        $services->set(ValidatorInterface::class, MockValidator::class)
            ->public();
            
        $services->set(PropertyAccessor::class, MockPropertyAccessor::class)
            ->public();
            
        $services->set(Security::class, MockSecurity::class)
            ->public();
            
        $services->set(LockService::class, MockLockService::class)
            ->public();
            
        $services->set(GrantService::class, MockGrantService::class)
            ->public();
            
        // 直接定义Tourze\JsonRPCSecurityBundle\Service\GrantService服务，不使用别名
        $services->set('Tourze\JsonRPCSecurityBundle\Service\GrantService', MockGrantService::class)
            ->public();
            
        // 注册AdminMenu服务和依赖
        $services->set(LinkGeneratorInterface::class, MockLinkGenerator::class)
            ->public();
            
        $services->set(AdminMenu::class)
            ->args([
                new Reference(LinkGeneratorInterface::class)
            ])
            ->public();
            
        // 模拟Redis相关服务
        $services->set('snc_redis.default', MockRedisClient::class)
            ->public();
            
        // 添加 snc_redis.lock 服务来解决 RedisClusterStore 的依赖问题
        $services->set('snc_redis.lock', MockRedisClient::class)
            ->public();
            
        // 设置doctrine.dbal.lock_connection别名
        $services->alias('doctrine.dbal.lock_connection', 'doctrine.dbal.default_connection')
            ->public();
            
        // 添加Twig环境
        $services->set(Environment::class, MockTwigEnvironment::class)
            ->public();
            
        // 添加DoctrineService
        $services->set(DoctrineService::class, MockDoctrineService::class)
            ->args([
                new Reference('doctrine.orm.entity_manager'),
            ])
            ->public();
            
        // 添加EntityLockService
        $services->set(EntityLockService::class, MockEntityLockService::class)
            ->args([
                new Reference('doctrine.orm.entity_manager'),
                new Reference(LockService::class),
            ])
            ->public();
            
        // 为依赖注入容器明确注册额外的服务别名
        $services->alias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getBaseProcedureLogger', LoggerInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getPropertyAccessor', PropertyAccessor::class)
            ->public();
            
        $services->alias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getEventDispatcher', EventDispatcherInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getValidator', ValidatorInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getCache', CacheInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getLockService', LockService::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getLockLogger', LoggerInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getSecurity', Security::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getCache', CacheInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getCacheLogger', LoggerInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getSecurity', Security::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getGrantService', GrantService::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCSecurityBundle\Procedure\SecurableProcedure::getSecurity', Security::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCSecurityBundle\Procedure\SecurableProcedure::getSecurityLogger', LoggerInterface::class)
            ->public();
            
        $services->alias('Tourze\JsonRPCSecurityBundle\Procedure\SecurableProcedure::getGrantService', GrantService::class)
            ->public();

        // 添加 CacheAdapter 相关服务
        $services->set(\Psr\Cache\CacheItemPoolInterface::class, MockCacheAdapter::class)
            ->public();
            
        // 为了向后兼容，也将 AdapterInterface 设置为同一个实现
        $services->alias(\Symfony\Component\Cache\Adapter\AdapterInterface::class, \Psr\Cache\CacheItemPoolInterface::class)
            ->public();
    }
    
    /**
     * 在container编译前的阶段注册所有服务
     */
    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        
        // 注册服务实现
        if (!$container->has(LoggerInterface::class)) {
            $container->register(LoggerInterface::class, MockLogger::class)
                ->setPublic(true);
        }
            
        if (!$container->has(PaginatorInterface::class)) {
            $container->register(PaginatorInterface::class, MockPaginator::class)
                ->setPublic(true);
        }
            
        if (!$container->has(CacheInterface::class)) {
            $container->register(CacheInterface::class, MockSimpleCache::class)
                ->setPublic(true);
        }
            
        if (!$container->has(EventDispatcherInterface::class)) {
            $container->register(EventDispatcherInterface::class, MockEventDispatcher::class)
                ->setPublic(true);
        }
            
        if (!$container->has(EventDispatcher::class)) {
            $container->register(EventDispatcher::class, MockEventDispatcher::class)
                ->setPublic(true);
        }
            
        if (!$container->has(ValidatorInterface::class)) {
            $container->register(ValidatorInterface::class, MockValidator::class)
                ->setPublic(true);
        }
            
        if (!$container->has(PropertyAccessor::class)) {
            $container->register(PropertyAccessor::class, MockPropertyAccessor::class)
                ->setPublic(true);
        }
            
        if (!$container->has(Security::class)) {
            $container->register(Security::class, MockSecurity::class)
                ->setPublic(true);
        }
            
        if (!$container->has(LockService::class)) {
            $container->register(LockService::class, MockLockService::class)
                ->setPublic(true);
        }
            
        if (!$container->has(GrantService::class)) {
            $container->register(GrantService::class, MockGrantService::class)
                ->setPublic(true);
        }
        
        // 注册特殊服务
        $container->setAlias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getBaseProcedureLogger', LoggerInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getPropertyAccessor', PropertyAccessor::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getEventDispatcher', EventDispatcherInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPC\Core\Procedure\BaseProcedure::getValidator', ValidatorInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getCache', CacheInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getLockService', LockService::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getLockLogger', LoggerInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::getSecurity', Security::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getCache', CacheInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getCacheLogger', LoggerInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getSecurity', Security::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure::getGrantService', GrantService::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCSecurityBundle\Procedure\SecurableProcedure::getSecurity', Security::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCSecurityBundle\Procedure\SecurableProcedure::getSecurityLogger', LoggerInterface::class)
            ->setPublic(true);
            
        $container->setAlias('Tourze\JsonRPCSecurityBundle\Procedure\SecurableProcedure::getGrantService', GrantService::class)
            ->setPublic(true);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/cms_bundle_tests/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/cms_bundle_tests/log';
    }
    
    public function getProjectDir(): string
    {
        return dirname(__DIR__, 3);
    }
} 