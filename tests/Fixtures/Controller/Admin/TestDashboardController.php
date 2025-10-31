<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Fixtures\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

/**
 * CMS Bundle 测试专用的 Dashboard 控制器.
 */
#[AdminDashboard(routePath: '/test-cms-admin', routeName: 'test_cms_admin')]
class TestDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return new Response('CMS Test Dashboard');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CMS Test Dashboard')
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [];
    }
}
