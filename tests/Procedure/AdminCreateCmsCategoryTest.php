<?php

namespace CmsBundle\Tests\Procedure;

use CmsBundle\Entity\Category;
use CmsBundle\Procedure\Category\AdminCreateCmsCategory;
use CmsBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AdminCreateCmsCategoryTest extends TestCase
{
    /**
     * 测试创建CMS分类
     */
    public function testExecute(): void
    {
        // 创建模拟对象
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 设置期望
        $categoryRepository->expects($this->any())
            ->method('find')
            ->willReturn(null);
        
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Category $category) {
                return $category->getTitle() === '测试分类' &&
                       $category->isValid() === true &&
                       $category->getSortNumber() === 10;
            }));
        
        $entityManager->expects($this->once())
            ->method('flush');
        
        // 创建并测试Procedure
        $procedure = new AdminCreateCmsCategory($categoryRepository, $entityManager);
        $procedure->title = '测试分类';
        $procedure->valid = true;
        $procedure->sortNumber = 10;
        
        $result = $procedure->execute();
        
        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('__message', $result);
        $this->assertEquals('创建成功', $result['__message']);
    }
    
    /**
     * 测试创建带父级的CMS分类
     */
    public function testExecuteWithParent(): void
    {
        // 创建父级分类
        $parentCategory = new Category();
        $parentCategory->setTitle('父级分类');
        $parentCategory->setValid(true);
        $parentCategory->setSortNumber(1);
        
        // 创建模拟对象
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 设置期望
        $categoryRepository->expects($this->once())
            ->method('find')
            ->with('parent123')
            ->willReturn($parentCategory);
        
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Category $category) use ($parentCategory) {
                return $category->getTitle() === '子分类' &&
                       $category->getParent() === $parentCategory;
            }));
        
        $entityManager->expects($this->once())
            ->method('flush');
        
        // 创建并测试Procedure
        $procedure = new AdminCreateCmsCategory($categoryRepository, $entityManager);
        $procedure->title = '子分类';
        $procedure->parentId = 'parent123';
        $procedure->valid = true;
        $procedure->sortNumber = 20;
        
        $result = $procedure->execute();
        
        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('__message', $result);
    }
    
    /**
     * 测试无效父级分类ID导致的异常
     */
    public function testExecuteWithInvalidParent(): void
    {
        // 创建模拟对象
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 设置期望 - 找不到父级分类
        $categoryRepository->expects($this->once())
            ->method('find')
            ->with('invalid-parent')
            ->willReturn(null);
        
        // 不应调用persist和flush方法
        $entityManager->expects($this->never())
            ->method('persist');
        $entityManager->expects($this->never())
            ->method('flush');
        
        // 创建并测试Procedure
        $procedure = new AdminCreateCmsCategory($categoryRepository, $entityManager);
        $procedure->title = '测试分类';
        $procedure->parentId = 'invalid-parent';
        
        // 期望抛出异常
        $this->expectException(\Tourze\JsonRPC\Core\Exception\ApiException::class);
        $this->expectExceptionMessage('找不到上级分类');
        
        $procedure->execute();
    }
} 