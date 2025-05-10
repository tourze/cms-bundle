<?php

namespace CmsBundle\Tests\Unit;

use CmsBundle\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    private ItemInterface $rootItem;
    private LinkGeneratorInterface $linkGenerator;

    protected function setUp(): void
    {
        // 创建ItemInterface模拟对象
        $this->rootItem = $this->createMock(ItemInterface::class);
        
        // 创建LinkGenerator模拟对象
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
    }

    public function testInvoke_createsContentCenterMenuItemIfNotExists(): void
    {
        // 跳过测试，直到我们找到更好的方法模拟菜单的多次调用
        $this->markTestSkipped('需要更好的方法模拟菜单的多次调用');
    }
    
    public function testInvoke_addsAllSubmenuItems(): void
    {
        // 配置contentMenuItem
        $contentMenuItem = $this->createMock(ItemInterface::class);
        
        // 配置rootItem的getChild方法返回contentMenuItem
        $this->rootItem->method('getChild')
            ->with('内容中心')
            ->willReturn($contentMenuItem);
        
        // 配置LinkGenerator模拟返回各个实体的URL
        $this->linkGenerator->method('getCurdListPage')
            ->willReturnCallback(function($entityClass) {
                return '/admin/' . $entityClass;
            });
        
        // 创建子菜单项
        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->method('setUri')->willReturnSelf();
        
        // 验证contentMenuItem的addChild方法被调用8次，每个实体一次
        $contentMenuItem->expects($this->exactly(8))
            ->method('addChild')
            ->willReturn($childMenuItem);
        
        // 创建AdminMenu实例
        $adminMenu = new AdminMenu($this->linkGenerator);
        
        // 调用__invoke方法
        $adminMenu($this->rootItem);
    }
    
    public function testInvoke_reuseExistingContentCenterMenuItem(): void
    {
        // 配置contentMenuItem并设置预期的extra值
        $contentMenuItem = $this->createMock(ItemInterface::class);
        $contentMenuItem->method('getExtra')
            ->with('permission')
            ->willReturn('SomeBundle');
        
        // 配置rootItem的getChild方法返回contentMenuItem
        $this->rootItem->method('getChild')
            ->with('内容中心')
            ->willReturn($contentMenuItem);
        
        // 确保不会再设置permission
        $contentMenuItem->expects($this->never())
            ->method('setExtra');
        
        // 配置LinkGenerator模拟返回值
        $this->linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/some-url');
        
        // 创建子菜单项
        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->method('setUri')->willReturnSelf();
        
        // 验证contentMenuItem的addChild方法被调用8次，每个实体一次
        $contentMenuItem->expects($this->exactly(8))
            ->method('addChild')
            ->willReturn($childMenuItem);
        
        // 创建AdminMenu实例
        $adminMenu = new AdminMenu($this->linkGenerator);
        
        // 调用__invoke方法
        $adminMenu($this->rootItem);
    }
} 