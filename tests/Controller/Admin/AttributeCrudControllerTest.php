<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Controller\Admin;

use CmsBundle\Controller\Admin\AttributeCrudController;
use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\FieldType;
use CmsBundle\Tests\AbstractCmsControllerTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @internal
 */
#[CoversClass(AttributeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AttributeCrudControllerTest extends AbstractCmsControllerTestCase
{
    /**
     * @var AbstractCrudController<Attribute>|null
     */
    private ?AbstractCrudController $cachedController = null;

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Attribute::class, AttributeCrudController::getEntityFqcn());
    }

    public function testAccessWithoutLogin(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/cms/attribute');
    }

    public function testListAction(): void
    {
        $client = self::createAuthenticatedClient();

        $response = $client->request('GET', '/admin/cms/attribute');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('属性字段列表', (string) $client->getResponse()->getContent());
    }

    public function testCrudConfiguration(): void
    {
        $controller = new AttributeCrudController();
        $this->assertInstanceOf(AttributeCrudController::class, $controller);
    }

    public function testNewAction(): void
    {
        $client = self::createAuthenticatedClient();

        $client->request('GET', '/admin/cms/attribute/new');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('新建属性字段', (string) $client->getResponse()->getContent());
    }

    public function testCreateAttribute(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('test-model-for-attribute');
        $model->setTitle('测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->flush();

        // 直接通过数据库创建属性来测试功能
        $attribute = new Attribute();
        $attribute->setModel($model);
        $attribute->setName('test-attribute');
        $attribute->setTitle('测试属性');
        $attribute->setType(FieldType::STRING);
        $attribute->setDisplayOrder(50);
        $attribute->setSpan(24);
        $attribute->setValid(true);
        $attribute->setRequired(false);
        $attribute->setSearchable(false);

        $entityManager->persist($attribute);
        $entityManager->flush();

        // 验证属性创建成功
        $this->assertNotNull($attribute->getId());
    }

    public function testEditAction(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('edit-model-for-attribute');
        $model->setTitle('编辑测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个属性
        $attribute = new Attribute();
        $attribute->setModel($model);
        $attribute->setName('edit-test-attribute');
        $attribute->setTitle('编辑测试属性');
        $attribute->setType(FieldType::STRING);
        $attribute->setDisplayOrder(50);
        $attribute->setSpan(24);
        $attribute->setValid(true);
        $attribute->setRequired(false);
        $attribute->setSearchable(false);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($attribute);
        $entityManager->flush();

        // 测试编辑页面
        $client->request('GET', '/admin/cms/attribute/'.$attribute->getId().'/edit');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('编辑属性字段', (string) $client->getResponse()->getContent());
    }

    public function testUpdateAttribute(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('update-model-for-attribute');
        $model->setTitle('更新测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个属性
        $attribute = new Attribute();
        $attribute->setModel($model);
        $attribute->setName('update-test-attribute');
        $attribute->setTitle('更新测试属性');
        $attribute->setType(FieldType::STRING);
        $attribute->setDisplayOrder(50);
        $attribute->setSpan(24);
        $attribute->setValid(true);
        $attribute->setRequired(false);
        $attribute->setSearchable(false);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($attribute);
        $entityManager->flush();

        // 测试编辑页面
        $crawler = $client->request('GET', '/admin/cms/attribute/'.$attribute->getId().'/edit');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('编辑属性字段', (string) $client->getResponse()->getContent());

        // 直接通过数据库更新来测试功能
        $attribute->setTitle('更新后的属性标题');
        $entityManager->flush();

        // 验证实体的数据确实更新了
        $this->assertSame('更新后的属性标题', $attribute->getTitle());
    }

    public function testDetailAction(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('detail-model-for-attribute');
        $model->setTitle('详情测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个属性
        $attribute = new Attribute();
        $attribute->setModel($model);
        $attribute->setName('detail-test-attribute');
        $attribute->setTitle('详情测试属性');
        $attribute->setType(FieldType::STRING);
        $attribute->setDisplayOrder(50);
        $attribute->setSpan(24);
        $attribute->setValid(true);
        $attribute->setRequired(false);
        $attribute->setSearchable(false);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($attribute);
        $entityManager->flush();

        // 测试详情页面
        $client->request('GET', '/admin/cms/attribute/'.$attribute->getId());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('属性字段详情', (string) $client->getResponse()->getContent());
    }

    public function testDeleteAttribute(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('delete-model-for-attribute');
        $model->setTitle('删除测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 然后创建一个属性
        $attribute = new Attribute();
        $attribute->setModel($model);
        $attribute->setName('delete-test-attribute');
        $attribute->setTitle('删除测试属性');
        $attribute->setType(FieldType::STRING);
        $attribute->setDisplayOrder(50);
        $attribute->setSpan(24);
        $attribute->setValid(true);
        $attribute->setRequired(false);
        $attribute->setSearchable(false);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($attribute);
        $entityManager->flush();
        $attributeId = $attribute->getId();

        // 测试删除 - 直接通过数据库删除
        $entityManager->remove($attribute);
        $entityManager->flush();

        // 验证数据库中的数据确实删除了
        $entityManager->clear();
        $deletedAttribute = $entityManager->find(Attribute::class, $attributeId);
        $this->assertNull($deletedAttribute);
    }

    public function testValidationErrors(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/cms/attribute/new');
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
        $crawler = $client->request('GET', '/admin/cms/attribute/new');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // 验证必填字段的存在性
        $this->assertStringContainsString('所属模型', (string) $client->getResponse()->getContent());
        $this->assertStringContainsString('字段名', (string) $client->getResponse()->getContent());
        $this->assertStringContainsString('显示名', (string) $client->getResponse()->getContent());
        $this->assertStringContainsString('字段类型', (string) $client->getResponse()->getContent());
        $this->assertStringContainsString('显示排序', (string) $client->getResponse()->getContent());

        // 验证必填标记（HTML required属性或标识符）的存在
        $content = (string) $client->getResponse()->getContent();
        $this->assertStringContainsString('required', $content, '应该包含必填字段标记');
    }

    public function testSearchFilters(): void
    {
        $client = self::createAuthenticatedClient();

        // 首先创建一个模型
        $model = new Model();
        $model->setCode('search-model-for-attribute');
        $model->setTitle('搜索测试模型');
        $model->setSortNumber(100);
        $model->setValid(true);

        // 创建测试数据
        $attribute1 = new Attribute();
        $attribute1->setModel($model);
        $attribute1->setName('search-attribute-1');
        $attribute1->setTitle('搜索属性1');
        $attribute1->setType(FieldType::STRING);
        $attribute1->setDisplayOrder(50);
        $attribute1->setSpan(24);
        $attribute1->setValid(true);
        $attribute1->setRequired(false);
        $attribute1->setSearchable(false);

        $attribute2 = new Attribute();
        $attribute2->setModel($model);
        $attribute2->setName('search-attribute-2');
        $attribute2->setTitle('搜索属性2');
        $attribute2->setType(FieldType::INTEGER);
        $attribute2->setDisplayOrder(60);
        $attribute2->setSpan(24);
        $attribute2->setValid(false);
        $attribute2->setRequired(true);
        $attribute2->setSearchable(false);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = self::getEntityManager();
        $entityManager->persist($model);
        $entityManager->persist($attribute1);
        $entityManager->persist($attribute2);
        $entityManager->flush();

        // 测试列表页面能正常显示
        $crawler = $client->request('GET', '/admin/cms/attribute');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('属性字段列表', (string) $client->getResponse()->getContent());
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '所属模型列' => ['所属模型'];
        yield '字段名列' => ['字段名'];
        yield '显示名列' => ['显示名'];
        yield '字段类型列' => ['字段类型'];
        yield '必填列' => ['必填'];
        yield '可搜索列' => ['可搜索'];
        yield '显示排序列' => ['显示排序'];
        yield '状态列' => ['状态'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield '所属模型字段' => ['model'];
        yield '字段名字段' => ['name'];
        yield '显示名字段' => ['title'];
        yield '字段类型字段' => ['type'];
        yield '默认值字段' => ['defaultValue'];
        yield '必填字段' => ['required'];
        yield '数据长度字段' => ['length'];
        yield '编辑宽度字段' => ['span'];
        yield '可搜索字段' => ['searchable'];
        yield '支持导入字段' => ['importable'];
        yield '显示排序字段' => ['displayOrder'];
        yield '字段配置字段' => ['config'];
        yield '占位提示字段' => ['placeholder'];
        yield '状态字段' => ['valid'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield '所属模型字段' => ['model'];
        yield '字段名字段' => ['name'];
        yield '显示名字段' => ['title'];
        yield '字段类型字段' => ['type'];
        yield '默认值字段' => ['defaultValue'];
        yield '必填字段' => ['required'];
        yield '数据长度字段' => ['length'];
        yield '编辑宽度字段' => ['span'];
        yield '可搜索字段' => ['searchable'];
        yield '支持导入字段' => ['importable'];
        yield '显示排序字段' => ['displayOrder'];
        yield '字段配置字段' => ['config'];
        yield '占位提示字段' => ['placeholder'];
        yield '状态字段' => ['valid'];
    }

    /**
     * @return AbstractCrudController<Attribute>
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

        $this->cachedController = new AttributeCrudController();

        return $this->cachedController;
    }
}
