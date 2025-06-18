<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Category;
use CmsBundle\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
        $this->category->setTitle('测试分类');
    }

    public function testConstructor_initializesCollections(): void
    {
        // 验证构造函数初始化了所有集合
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getEntities());
        $this->assertCount(0, $this->category->getEntities());

        $this->assertInstanceOf(ArrayCollection::class, $this->category->getChildren());
        $this->assertCount(0, $this->category->getChildren());
    }

    public function testGettersAndSetters_forScalarProperties(): void
    {
        // 测试标题的getter和setter
        $this->assertEquals('测试分类', $this->category->getTitle());
        $this->category->setTitle('新分类标题');
        $this->assertEquals('新分类标题', $this->category->getTitle());

        // 测试描述的getter和setter
        $this->assertNull($this->category->getDescription());
        $this->category->setDescription('测试描述');
        $this->assertEquals('测试描述', $this->category->getDescription());

        // 测试排序号的getter和setter
        $this->assertEquals(0, $this->category->getSortNumber());
        $this->category->setSortNumber(10);
        $this->assertEquals(10, $this->category->getSortNumber());

        // 测试有效状态的getter和setter
        $this->assertFalse($this->category->isValid());
        $this->category->setValid(true);
        $this->assertTrue($this->category->isValid());
    }

    public function testEntitiesRelationship_addsAndRemovesEntities(): void
    {
        // 创建模拟的Entity对象
        $entity1 = $this->createMock(Entity::class);
        $entity1->method('getCategories')->willReturn(new ArrayCollection());

        $entity2 = $this->createMock(Entity::class);
        $entity2->method('getCategories')->willReturn(new ArrayCollection());

        // 添加Entity到Category
        $this->category->addEntity($entity1);
        $this->category->addEntity($entity2);

        // 验证Entities集合中包含添加的Entity
        $this->assertCount(2, $this->category->getEntities());
        $this->assertTrue($this->category->getEntities()->contains($entity1));
        $this->assertTrue($this->category->getEntities()->contains($entity2));

        // 移除一个Entity
        $this->category->removeEntity($entity1);

        // 验证Entities集合被更新
        $this->assertCount(1, $this->category->getEntities());
        $this->assertFalse($this->category->getEntities()->contains($entity1));
        $this->assertTrue($this->category->getEntities()->contains($entity2));
    }

    public function testParentChildRelationship_setsParentAndAddsChild(): void
    {
        // 创建父分类
        $parentCategory = new Category();
        $parentCategory->setTitle('父分类');

        // 设置父分类
        $this->category->setParent($parentCategory);

        // 验证父子关系正确设置
        $this->assertSame($parentCategory, $this->category->getParent());

        // 测试移除父分类关系
        $this->category->setParent(null);
        $this->assertNull($this->category->getParent());
    }

    public function testChildrenRelationship_addsAndRemovesChildren(): void
    {
        // 创建子分类
        $childCategory1 = new Category();
        $childCategory1->setTitle('子分类1');

        $childCategory2 = new Category();
        $childCategory2->setTitle('子分类2');

        // 添加子分类
        $this->category->addChild($childCategory1);
        $this->category->addChild($childCategory2);

        // 验证子分类关系正确设置
        $this->assertCount(2, $this->category->getChildren());
        $this->assertTrue($this->category->getChildren()->contains($childCategory1));
        $this->assertTrue($this->category->getChildren()->contains($childCategory2));

        // 移除一个子分类
        $this->category->removeChild($childCategory1);

        // 验证子分类关系正确更新
        $this->assertCount(1, $this->category->getChildren());
        $this->assertFalse($this->category->getChildren()->contains($childCategory1));
        $this->assertTrue($this->category->getChildren()->contains($childCategory2));
    }

    public function testToString_returnsTitle(): void
    {
        // 测试__toString方法
        $this->category->setTitle('分类标题');

        // 如果没有id，返回空字符串
        $this->assertEquals('', (string) $this->category);

        // 设置一个模拟的ID，通过反射设置private属性
        $reflection = new \ReflectionClass($this->category);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->category, 1);

        // 验证__toString方法返回格式为"title(id)"
        $expected = '分类标题(1)';
        $this->assertEquals($expected, (string) $this->category);
    }

    public function testRetrieveAdminArray_returnsExpectedStructure(): void
    {
        // 设置必要的属性
        $this->category->setTitle('测试分类');
        $this->category->setDescription('测试描述');
        $this->category->setSortNumber(10);

        // 调用retrieveAdminArray方法
        $array = $this->category->retrieveAdminArray();

        // 验证返回的数组结构
        $this->assertArrayHasKey('title', $array);
        $this->assertEquals('测试分类', $array['title']);
        $this->assertArrayHasKey('description', $array);
        $this->assertEquals('测试描述', $array['description']);
        $this->assertArrayHasKey('sortNumber', $array);
        $this->assertEquals(10, $array['sortNumber']);
    }
}
