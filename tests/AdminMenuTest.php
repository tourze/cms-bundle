<?php

namespace CmsBundle\Tests;

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
        // 创建新菜单项
        $newMenuItem = $this->createMock(ItemInterface::class);
        $newMenuItem->expects($this->once())
            ->method('setExtra')
            ->with('permission', 'CmsBundle')
            ->willReturnSelf();

        // 配置rootItem - 第一次返回null，后续返回新创建的菜单项
        $this->rootItem->method('getChild')
            ->with('内容中心')
            ->willReturnOnConsecutiveCalls(
                null, // 第一次调用返回null，触发创建菜单
                $newMenuItem,
                $newMenuItem,
                $newMenuItem,
                $newMenuItem,
                $newMenuItem,
                $newMenuItem,
                $newMenuItem // 后续7次返回创建的菜单项
            );

        // 验证addChild被调用来创建新的"内容中心"菜单项
        $this->rootItem->expects($this->once())
            ->method('addChild')
            ->with('内容中心')
            ->willReturn($newMenuItem);

        // 配置LinkGenerator返回URL
        $this->linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test-url');

        // 创建子菜单项模拟
        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->method('setUri')->willReturnSelf();
        $newMenuItem->method('addChild')->willReturn($childMenuItem);

        // 创建AdminMenu实例并调用
        $adminMenu = new AdminMenu($this->linkGenerator);
        $adminMenu($this->rootItem);
    }

    public function testInvoke_addsAllSubmenuItems(): void
    {
        // 配置contentMenuItem
        $contentMenuItem = $this->createMock(ItemInterface::class);

        // 配置rootItem的getChild方法返回contentMenuItem
        $this->rootItem->method('getChild')
            ->with('内容中心')
            ->willReturn($contentMenuItem);

        // 配置LinkGenerator模拟返回URL
        $this->linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test-url');

        // 创建子菜单项
        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->method('setUri')->willReturnSelf();

        // 验证contentMenuItem的addChild方法被调用7次，每个实体一次
        $contentMenuItem->expects($this->exactly(7))
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

        // 验证contentMenuItem的addChild方法被调用7次，每个实体一次
        $contentMenuItem->expects($this->exactly(7))
            ->method('addChild')
            ->willReturn($childMenuItem);

        // 创建AdminMenu实例
        $adminMenu = new AdminMenu($this->linkGenerator);

        // 调用__invoke方法
        $adminMenu($this->rootItem);
    }

    public function testInvoke_callsLinkGeneratorForAllEntities(): void
    {
        // 测试确保为每个实体类调用LinkGenerator
        $contentMenuItem = $this->createMock(ItemInterface::class);
        $this->rootItem->method('getChild')->willReturn($contentMenuItem);

        // 验证LinkGenerator被调用7次，每个实体类一次
        $this->linkGenerator->expects($this->exactly(7))
            ->method('getCurdListPage')
            ->willReturn('/admin/test-url');

        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->method('setUri')->willReturnSelf();
        $contentMenuItem->method('addChild')->willReturn($childMenuItem);

        $adminMenu = new AdminMenu($this->linkGenerator);
        $adminMenu($this->rootItem);
    }

    public function testInvoke_handlesEmptyLinkGeneratorResponse(): void
    {
        // 测试LinkGenerator返回空字符串的情况
        $contentMenuItem = $this->createMock(ItemInterface::class);
        $this->rootItem->method('getChild')->willReturn($contentMenuItem);

        // LinkGenerator返回空URL
        $this->linkGenerator->method('getCurdListPage')->willReturn('');

        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->expects($this->exactly(7))
            ->method('setUri')
            ->with('') // 空字符串应该被正确处理
            ->willReturnSelf();

        $contentMenuItem->method('addChild')->willReturn($childMenuItem);

        $adminMenu = new AdminMenu($this->linkGenerator);
        $adminMenu($this->rootItem);
    }
}
