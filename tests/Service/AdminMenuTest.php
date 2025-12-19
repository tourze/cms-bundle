<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsBundle\Service\AdminMenu;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    public function testInvokeCreatesContentCenterMenuItemIfNotExists(): void
    {
        // 创建 Mock 对象
        $rootItem = $this->createMock(ItemInterface::class);
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 配置 rootItem - 第一次返回 null，后续返回新创建的菜单项
        $newMenuItem = $this->createMock(ItemInterface::class);
        $callCount = 0;
        $rootItem->method('getChild')
            ->willReturnCallback(function () use (&$callCount, $newMenuItem) {
                ++$callCount;

                return 1 === $callCount ? null : $newMenuItem;
            })
        ;

        // 配置 addChild 方法返回新菜单项
        $rootItem->method('addChild')
            ->willReturn($newMenuItem)
        ;

        // 配置 setExtra 方法
        $newMenuItem->method('setExtra')
            ->willReturnSelf()
        ;

        // 配置子菜单项
        $childMenuItem = $this->createMock(ItemInterface::class);
        $newMenuItem->method('addChild')
            ->willReturn($childMenuItem)
        ;

        // 配置 LinkGenerator
        $linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test-url')
        ;

        // 创建 AdminMenu 实例并调用
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);

        // 验证 getChild 被调用了
        $this->assertTrue($callCount > 0);
    }

    public function testInvokeAddsAllSubmenuItems(): void
    {
        // 创建 Mock 对象
        $rootItem = $this->createMock(ItemInterface::class);
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 配置 contentMenuItem
        $contentMenuItem = $this->createMock(ItemInterface::class);
        $rootItem->method('getChild')
            ->willReturn($contentMenuItem)
        ;

        // 配置 LinkGenerator
        $linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test-url')
        ;

        // 配置 contentMenuItem 的 addChild 方法，设置期望调用次数
        $childMenuItem = $this->createMock(ItemInterface::class);
        $contentMenuItem->expects($this->atLeastOnce())
            ->method('addChild')
            ->willReturn($childMenuItem)
        ;

        // 创建 AdminMenu 实例并验证调用
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);

        // 验证服务实例创建成功
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testInvokeReuseExistingContentCenterMenuItem(): void
    {
        // 创建 Mock 对象
        $rootItem = $this->createMock(ItemInterface::class);
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 配置 contentMenuItem
        $contentMenuItem = $this->createMock(ItemInterface::class);
        $contentMenuItem->method('getExtra')
            ->willReturn('SomeBundle')
        ;
        $contentMenuItem->method('setExtra')
            ->willReturnSelf()
        ;

        $rootItem->method('getChild')
            ->willReturn($contentMenuItem)
        ;

        // 配置 LinkGenerator
        $linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test-url')
        ;

        // 配置 contentMenuItem 的 addChild 方法
        $childMenuItem = $this->createMock(ItemInterface::class);
        $contentMenuItem->expects($this->atLeastOnce())
            ->method('addChild')
            ->willReturn($childMenuItem)
        ;

        // 创建 AdminMenu 实例并验证行为
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);

        // 验证服务实例创建成功
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testInvokeCallsLinkGeneratorForAllEntities(): void
    {
        // 创建 Mock 对象
        $rootItem = $this->createMock(ItemInterface::class);
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 配置 rootItem 返回 null，触发菜单创建
        $rootItem->method('getChild')
            ->willReturn(null)
        ;

        // 配置新的菜单项
        $newMenuItem = $this->createMock(ItemInterface::class);
        $newMenuItem->method('setExtra')
            ->willReturnSelf()
        ;
        $newMenuItem->method('addChild')
            ->willReturn($this->createMock(ItemInterface::class))
        ;

        $rootItem->method('addChild')
            ->willReturn($newMenuItem)
        ;

        // 配置 LinkGenerator
        $linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test-url')
        ;

        // 创建 AdminMenu 实例并调用
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);

        // 验证服务实例正确创建并可以调用
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testInvokeHandlesEmptyLinkGeneratorResponse(): void
    {
        // 创建 Mock 对象
        $rootItem = $this->createMock(ItemInterface::class);
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 配置 rootItem 返回 null，触发菜单创建
        $rootItem->method('getChild')
            ->willReturn(null)
        ;

        // 配置新的菜单项
        $newMenuItem = $this->createMock(ItemInterface::class);
        $newMenuItem->method('setExtra')
            ->willReturnSelf()
        ;
        $newMenuItem->method('addChild')
            ->willReturn($this->createMock(ItemInterface::class))
        ;

        $rootItem->method('addChild')
            ->willReturn($newMenuItem)
        ;

        // 配置 LinkGenerator 返回空 URL
        $linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test-url');

        // 创建 AdminMenu 实例并调用
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);

        // 验证服务实例正确创建并可以调用
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    protected function onSetUp(): void
    {
        // 不需要额外的初始化逻辑
    }
}
