<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\CmsBundle\Entity\VisitStat;

/**
 * 访问统计管理控制器.
 *
 * @template TEntity of VisitStat
 *
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(routePath: '/cms/visit-stat', routeName: 'cms_visit_stat')]
final class VisitStatCrudController extends AbstractCrudController
{
    /**
     * @return class-string<VisitStat>
     */
    public static function getEntityFqcn(): string
    {
        return VisitStat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('访问统计')
            ->setEntityLabelInPlural('访问统计管理')
            ->setPageTitle('index', '访问统计列表')
            ->setPageTitle('detail', '访问统计详情')
            ->setHelp('index', '查看内容的访问统计数据，分析热门内容和访问趋势')
            ->setDefaultSort(['date' => 'DESC', 'value' => 'DESC'])
            ->setSearchFields(['entityId'])
            // 访问统计通常只用于查看分析，不允许编辑
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setMaxLength(9999)
        ;

        yield DateField::new('date', '统计日期')
            ->setHelp('统计数据的日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield TextField::new('entityId', '内容ID')
            ->setHelp('被访问的内容实体ID')
        ;

        yield IntegerField::new('value', '访问次数')
            ->setHelp('该日期该内容的访问次数')
            ->formatValue(function ($value) {
                return number_format(is_numeric($value) ? (int) $value : 0);
            })
        ;

        // 审计字段
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // 访问统计只允许查看，不允许编辑和删除
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('entityId', '内容ID'))
            ->add(NumericFilter::new('value', '访问次数'))
            ->add(DateTimeFilter::new('date', '统计日期'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
