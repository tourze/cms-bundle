<?php

namespace CmsBundle\Tests\Integration;

use CmsBundle\AdminMenu;
use CmsBundle\Entity\Category;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Repository\CategoryRepository;
use CmsBundle\Repository\EntityRepository;
use CmsBundle\Repository\ModelRepository;
use CmsBundle\Service\ContentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class CmsBundleIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
        
        // 创建数据库架构
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        
        try {
            $schemaTool->createSchema($metadata);
        } catch (\Exception $e) {
            // 如果架构已存在，可能会抛出异常，此处忽略
        }
    }

    /**
     * 测试Bundle注册和加载是否正常
     */
    public function testBundleRegistration(): void
    {
        $kernel = self::$kernel;
        $container = self::getContainer();
        
        // 验证CmsBundle是否已注册
        $bundles = $kernel->getBundles();
        $this->assertArrayHasKey('CmsBundle', $bundles);
        
        // 验证CmsBundle的服务是否已注册
        $this->assertTrue($container->has(CategoryRepository::class));
        $this->assertTrue($container->has(EntityRepository::class));
        $this->assertTrue($container->has(ModelRepository::class));
        $this->assertTrue($container->has(ContentService::class));
    }
    
    /**
     * 测试AdminMenu服务是否正常工作
     */
    public function testAdminMenuService(): void
    {
        $container = self::getContainer();
        
        // 获取AdminMenu服务
        $this->assertTrue($container->has(AdminMenu::class));
        $adminMenu = $container->get(AdminMenu::class);
        
        // 验证AdminMenu服务实例类型
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
        
        // 验证依赖注入是否正确
        $reflection = new \ReflectionClass(AdminMenu::class);
        $property = $reflection->getProperty('linkGenerator');
        $property->setAccessible(true);
        
        $linkGenerator = $property->getValue($adminMenu);
        $this->assertInstanceOf(LinkGeneratorInterface::class, $linkGenerator);
    }
    
    /**
     * 测试ContentService服务是否正常工作
     */
    public function testContentServiceInjection(): void
    {
        $container = self::getContainer();
        
        // 获取ContentService服务
        $this->assertTrue($container->has(ContentService::class));
        $contentService = $container->get(ContentService::class);
        
        // 验证ContentService服务实例类型
        $this->assertInstanceOf(ContentService::class, $contentService);
        
        // 验证依赖注入是否正确
        $reflection = new \ReflectionClass(ContentService::class);
        
        $valueRepoProperty = $reflection->getProperty('valueRepository');
        $valueRepoProperty->setAccessible(true);
        $this->assertNotNull($valueRepoProperty->getValue($contentService));
        
        $modelRepoProperty = $reflection->getProperty('modelRepository');
        $modelRepoProperty->setAccessible(true);
        $this->assertNotNull($modelRepoProperty->getValue($contentService));
    }
    
    /**
     * 测试实体仓库服务是否正常工作
     */
    public function testEntityRepositories(): void
    {
        $container = self::getContainer();
        
        // 获取实体仓库服务
        $categoryRepository = $container->get(CategoryRepository::class);
        $entityRepository = $container->get(EntityRepository::class);
        $modelRepository = $container->get(ModelRepository::class);
        
        // 验证实体仓库服务实例类型
        $this->assertInstanceOf(CategoryRepository::class, $categoryRepository);
        $this->assertInstanceOf(EntityRepository::class, $entityRepository);
        $this->assertInstanceOf(ModelRepository::class, $modelRepository);
        
        // 验证实体仓库是否能正常获取EntityManager
        $entityManager = $this->getEntityManager($categoryRepository);
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
    }
    
    /**
     * 测试实体映射是否正确
     */
    public function testEntityMapping(): void
    {
        $container = self::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        
        // 验证核心实体类型是否已正确映射
        $this->assertTrue($entityManager->getMetadataFactory()->hasMetadataFor(Entity::class));
        $this->assertTrue($entityManager->getMetadataFactory()->hasMetadataFor(Category::class));
        $this->assertTrue($entityManager->getMetadataFactory()->hasMetadataFor(Model::class));
        
        // 验证实体关系映射
        $categoryMetadata = $entityManager->getClassMetadata(Category::class);
        $this->assertArrayHasKey('entities', $categoryMetadata->associationMappings);
        
        $entityMetadata = $entityManager->getClassMetadata(Entity::class);
        $this->assertArrayHasKey('categories', $entityMetadata->associationMappings);
        $this->assertArrayHasKey('model', $entityMetadata->associationMappings);
    }
    
    /**
     * 测试数据库架构生成
     */
    public function testSchemaCreation(): void
    {
        $container = self::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        
        // 创建数据库架构
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        
        try {
            // 先尝试删除现有的架构
            $schemaTool->dropSchema($metadata);
        } catch (\Exception $e) {
            // 忽略删除错误，可能架构不存在
        }
        
        try {
            $schemaTool->createSchema($metadata);
            $this->assertTrue(true, '数据库架构创建成功');
        } catch (\Exception $e) {
            $this->fail('数据库架构创建失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 从仓库中获取EntityManager
     */
    private function getEntityManager(object $repository): EntityManagerInterface
    {
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('getEntityManager');
        $method->setAccessible(true);
        
        return $method->invoke($repository);
    }
} 