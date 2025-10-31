<?php

declare(strict_types=1);

namespace CmsBundle\Controller\Admin;

use CmsBundle\Entity\Entity;
use CmsBundle\Enum\EntityState;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Tourze\TagManageBundle\Controller\Admin\TagCrudController;

/**
 * 内容实体管理控制器.
 *
 * @template TEntity of Entity
 *
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(routePath: '/cms/entity', routeName: 'cms_entity')]
final class EntityCrudController extends AbstractCrudController
{
    /**
     * @return class-string<Entity>
     */
    public static function getEntityFqcn(): string
    {
        return Entity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('内容')
            ->setEntityLabelInPlural('内容管理')
            ->setPageTitle('index', '内容列表')
            ->setPageTitle('new', '新建内容')
            ->setPageTitle('edit', '编辑内容')
            ->setPageTitle('detail', '内容详情')
            ->setHelp('index', '管理所有内容实体，支持不同状态的内容发布和管理')
            ->setDefaultSort(['publishTime' => 'DESC', 'id' => 'DESC'])
            ->setSearchFields(['title'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setMaxLength(9999)
        ;

        yield TextField::new('title', '标题')
            ->setHelp('内容的标题')
            ->setRequired(true)
        ;

        yield AssociationField::new('model', '内容模型')
            ->setRequired(true)
            ->setCrudController(ModelCrudController::class)
            ->autocomplete()
            ->setHelp('选择内容所属的模型，模型决定了可用的属性字段')
        ;

        yield ChoiceField::new('state', '发布状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => EntityState::class])
            ->formatValue(function ($value) {
                return $value instanceof EntityState ? $value->getLabel() : '';
            })
            ->setRequired(true)
            ->setHelp('内容的发布状态')
        ;

        yield DateTimeField::new('publishTime', '发布时间')
            ->setHelp('内容的开始展示时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('endTime', '结束时间')
            ->setHelp('内容的结束展示时间')
            ->hideOnIndex()
        ;

        yield IntegerField::new('sortNumber', '排序编号')
            ->setHelp('数值越大排序越靠前')
            ->hideOnIndex()
        ;

        yield TextareaField::new('remark', '备注信息')
            ->setHelp('内容的备注或说明信息')
            ->hideOnIndex()
            ->setNumOfRows(3)
        ;

        // 关联字段 - 注释掉不存在的 catalogs 关联
        // yield AssociationField::new('catalogs', '所属分类')
        //     ->setHelp('内容所属的分类，支持多选')
        //     ->hideOnIndex()
        //     ->autocomplete()
        //     ->setCrudController(CatalogCrudController::class)
        // ;

        yield AssociationField::new('tags', '关联标签')
            ->setHelp('内容的标签，支持多选')
            ->setCrudController(TagCrudController::class)
            ->hideOnIndex()
            ->autocomplete()
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
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        // 构建状态选项
        $stateChoices = [];
        foreach (EntityState::cases() as $case) {
            $stateChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('title', '标题'))
            ->add(EntityFilter::new('model', '内容模型'))
            ->add(ChoiceFilter::new('state', '发布状态')->setChoices($stateChoices))
            ->add(DateTimeFilter::new('publishTime', '发布时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            // ->add(EntityFilter::new('catalogs', '所属分类')) // 注释掉不存在的关联
            ->add(EntityFilter::new('tags', '关联标签'))
        ;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $title = $entityInstance->getTitle();

        parent::deleteEntity($entityManager, $entityInstance);
        $this->addFlash('success', \sprintf('内容"%s"删除成功！', $title));
    }
}
