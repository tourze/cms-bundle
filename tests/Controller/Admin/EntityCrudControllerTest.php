<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Controller\Admin;

use CmsBundle\Controller\Admin\EntityCrudController;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\EntityState;
use CmsBundle\Tests\AbstractCmsControllerTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @internal
 */
#[CoversClass(EntityCrudController::class)]
#[RunTestsInSeparateProcesses]
final class EntityCrudControllerTest extends AbstractCmsControllerTestCase
{
    /**
     * @var AbstractCrudController<Entity>|null
     */
    private ?AbstractCrudController $cachedController = null;

    public function testAccessWithoutLogin(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/cms/entity');
    }

    public function testListAction(): void
    {
        $client = self::createAuthenticatedClient();

        $client->request('GET', '/admin/cms/entity');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('内容列表', (string) $client->getResponse()->getContent());
    }

    public function testCrudConfiguration(): void
    {
        $controller = new EntityCrudController();
        $this->assertInstanceOf(EntityCrudController::class, $controller);
    }

    public function testNewAction(): void
    {
        $client = self::createAuthenticatedClient();

        $client->request('GET', '/admin/cms/entity/new');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('新建内容', (string) $client->getResponse()->getContent());
    }

    public function testCreateEntity(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('test-model-for-entity');
        $model->setTitle('测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->flush();

        // 直接通过数据库创建实体来测试功能
        $entity = new Entity();
        $entity->setModel($model);
        $entity->setTitle('测试内容');
        $entity->setSortNumber(50);
        $entity->setState(EntityState::DRAFT);

        $entityManager->persist($entity);
        $entityManager->flush();

        // 验证数据是否保存成功
        $this->assertNotNull($entity->getId());
        $this->assertSame('测试内容', $entity->getTitle());

        // 验证新建页面能正常访问
        $crawler = $client->request('GET', '/admin/cms/entity/new');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('新建内容', (string) $client->getResponse()->getContent());
    }

    public function testEditAction(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('edit-model-for-entity');
        $model->setTitle('编辑测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个实体
        $entity = new Entity();
        $entity->setModel($model);
        $entity->setTitle('编辑测试内容');
        $entity->setSortNumber(50);
        $entity->setState(EntityState::DRAFT);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($entity);
        $entityManager->flush();

        // 测试编辑页面
        $client->request('GET', '/admin/cms/entity/'.$entity->getId().'/edit');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('编辑内容', (string) $client->getResponse()->getContent());
    }

    public function testUpdateEntity(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('update-model-for-entity');
        $model->setTitle('更新测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个实体
        $entity = new Entity();
        $entity->setModel($model);
        $entity->setTitle('更新测试内容');
        $entity->setSortNumber(50);
        $entity->setState(EntityState::DRAFT);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($entity);
        $entityManager->flush();

        // 测试编辑页面
        $crawler = $client->request('GET', '/admin/cms/entity/'.$entity->getId().'/edit');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('编辑内容', (string) $client->getResponse()->getContent());

        // 重新获取实体以确保它在当前的 EntityManager 中被管理
        $managedEntity = $entityManager->find(Entity::class, $entity->getId());
        $this->assertNotNull($managedEntity, 'Entity should exist in database');

        // 直接通过数据库更新来测试功能
        $managedEntity->setTitle('更新后的内容标题');
        $managedEntity->setState(EntityState::PUBLISHED);
        $entityManager->flush();

        // 验证数据库中的数据确实更新了
        $entityManager->clear(); // 清除缓存确保重新从数据库加载
        $updatedEntity = $entityManager->find(Entity::class, $entity->getId());
        $this->assertNotNull($updatedEntity, 'Updated entity should not be null');
        $this->assertSame('更新后的内容标题', $updatedEntity->getTitle());
        $this->assertSame(EntityState::PUBLISHED, $updatedEntity->getState());
    }

    public function testDetailAction(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('detail-model-for-entity');
        $model->setTitle('详情测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个实体
        $entity = new Entity();
        $entity->setModel($model);
        $entity->setTitle('详情测试内容');
        $entity->setSortNumber(50);
        $entity->setState(EntityState::DRAFT);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($entity);
        $entityManager->flush();

        // 测试详情页面
        $client->request('GET', '/admin/cms/entity/'.$entity->getId());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('内容详情', (string) $client->getResponse()->getContent());
    }

    public function testDeleteEntity(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('delete-model-for-entity');
        $model->setTitle('删除测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个实体
        $entity = new Entity();
        $entity->setModel($model);
        $entity->setTitle('删除测试内容');
        $entity->setSortNumber(50);
        $entity->setState(EntityState::DRAFT);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($entity);
        $entityManager->flush();
        $entityId = $entity->getId();

        // 验证实体创建成功
        $this->assertNotNull($entityId);
        $this->assertSame('删除测试内容', $entity->getTitle());

        // 直接删除实体来测试功能
        $entityManager->remove($entity);
        $entityManager->flush();

        // 验证实体已被删除
        $deletedEntity = $entityManager->find(Entity::class, $entityId);
        $this->assertNull($deletedEntity, 'Entity should be deleted');

        // 验证控制器方法存在
        $controller = new EntityCrudController();
        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->hasMethod('deleteEntity'), 'EntityCrudController should have deleteEntity method');
    }

    public function testValidationErrors(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/cms/entity/new');
        $form = $crawler->selectButton('Create')->form();

        // 提交空表单
        $client->submit($form);

        $this->assertSame(422, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('should not be blank', (string) $client->getResponse()->getContent());
    }

    public function testRequiredFieldValidation(): void
    {
        $client = self::createAuthenticatedClient();

        // 测试所有必填字段的验证
        $crawler = $client->request('GET', '/admin/cms/entity/new');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // 验证必填字段的存在性
        $this->assertStringContainsString('内容模型', (string) $client->getResponse()->getContent());
        $this->assertStringContainsString('标题', (string) $client->getResponse()->getContent());

        // 验证必填标记（HTML required属性或标识符）的存在
        $content = (string) $client->getResponse()->getContent();
        $this->assertStringContainsString('required', $content, '应该包含必填字段标记');
    }

    public function testSearchFilters(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('search-model-for-entity');
        $model->setTitle('搜索测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 创建测试数据
        $entity1 = new Entity();
        $entity1->setModel($model);
        $entity1->setTitle('搜索内容1');
        $entity1->setSortNumber(50);
        $entity1->setState(EntityState::DRAFT);

        $entity2 = new Entity();
        $entity2->setModel($model);
        $entity2->setTitle('搜索内容2');
        $entity2->setSortNumber(60);
        $entity2->setState(EntityState::DRAFT);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($entity1);
        $entityManager->persist($entity2);
        $entityManager->flush();

        // 测试列表页面能正常显示
        $crawler = $client->request('GET', '/admin/cms/entity');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('内容列表', (string) $client->getResponse()->getContent());

        // 验证实体创建成功
        $this->assertNotNull($entity1->getId());
        $this->assertNotNull($entity2->getId());
        $this->assertSame('搜索内容1', $entity1->getTitle());
        $this->assertSame('搜索内容2', $entity2->getTitle());
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '标题列' => ['标题'];
        yield '内容模型列' => ['内容模型'];
        yield '发布状态列' => ['发布状态'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield '标题字段' => ['title'];
        yield '内容模型字段' => ['model'];
        yield '发布状态字段' => ['state'];
        yield '发布时间字段' => ['publishTime'];
        yield '结束时间字段' => ['endTime'];
        yield '排序编号字段' => ['sortNumber'];
        yield '备注信息字段' => ['remark'];
        yield '关联标签字段' => ['tags'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield '标题字段' => ['title'];
        yield '内容模型字段' => ['model'];
        yield '发布状态字段' => ['state'];
        yield '发布时间字段' => ['publishTime'];
        yield '结束时间字段' => ['endTime'];
        yield '排序编号字段' => ['sortNumber'];
        yield '备注信息字段' => ['remark'];
        yield '关联标签字段' => ['tags'];
    }

    /**
     * @return AbstractCrudController<Entity>
     */
    protected function getControllerService(): AbstractCrudController
    {
        if (null !== $this->cachedController) {
            return $this->cachedController;
        }

        // 确保创建客户端（会自动设置为全局客户端）
        $client = static::createClient();
        $container = $client->getContainer();
        $adminUrlGenerator = $container->get(\EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator::class);
        $this->assertInstanceOf(\EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator::class, $adminUrlGenerator);

        $this->cachedController = new EntityCrudController();

        return $this->cachedController;
    }
}
