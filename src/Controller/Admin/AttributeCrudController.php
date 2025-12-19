<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Tourze\CmsBundle\Entity\Attribute;
use Tourze\CmsBundle\Enum\FieldType;

/**
 * 属性字段管理控制器.
 *
 * @template TEntity of Attribute
 *
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(routePath: '/cms/attribute', routeName: 'cms_attribute')]
final class AttributeCrudController extends AbstractCrudController
{
    /**
     * @return class-string<Attribute>
     */
    public static function getEntityFqcn(): string
    {
        return Attribute::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('属性字段')
            ->setEntityLabelInPlural('属性字段管理')
            ->setPageTitle('index', '属性字段列表')
            ->setPageTitle('new', '新建属性字段')
            ->setPageTitle('edit', '编辑属性字段')
            ->setPageTitle('detail', '属性字段详情')
            ->setHelp('index', '管理内容模型的字段定义和配置，支持多种数据类型和验证规则')
            ->setDefaultSort(['displayOrder' => 'DESC', 'id' => 'DESC'])
            ->setSearchFields(['name', 'title'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setMaxLength(9999)
        ;

        yield AssociationField::new('model', '所属模型')
            ->setRequired(true)
            ->setCrudController(ModelCrudController::class)
            ->autocomplete()
            ->setHelp('选择该属性所属的模型')
        ;

        yield TextField::new('name', '字段名')
            ->setHelp('英文字段名，用于程序调用')
            ->setRequired(true)
        ;

        yield TextField::new('title', '显示名')
            ->setHelp('字段的中文显示名称')
            ->setRequired(true)
        ;

        yield ChoiceField::new('type', '字段类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => FieldType::class])
            ->formatValue(function ($value) {
                return $value instanceof FieldType ? $value->getLabel() : '';
            })
            ->setRequired(true)
            ->setHelp('选择字段的数据类型')
        ;

        yield TextField::new('defaultValue', '默认值')
            ->hideOnIndex()
            ->setHelp('字段的默认值')
        ;

        yield BooleanField::new('required', '必填')
            ->setHelp('是否为必填字段')
        ;

        yield IntegerField::new('length', '数据长度')
            ->hideOnIndex()
            ->setHelp('字段的最大长度限制')
        ;

        yield IntegerField::new('span', '编辑宽度')
            ->hideOnIndex()
            ->setHelp('编辑页面的显示宽度(1-24)')
            ->setRequired(false)
        ;

        yield BooleanField::new('searchable', '可搜索')
            ->setHelp('是否支持搜索功能')
        ;

        yield BooleanField::new('importable', '支持导入')
            ->hideOnIndex()
            ->setHelp('是否支持数据导入')
        ;

        yield IntegerField::new('displayOrder', '显示排序')
            ->setHelp('数值越大排序越靠前')
            ->setRequired(true)
        ;

        yield TextareaField::new('config', '字段配置')
            ->hideOnIndex()
            ->setHelp('字段的JSON配置信息，用于选择类型字段的选项配置')
            ->setNumOfRows(6)
        ;

        yield TextField::new('placeholder', '占位提示')
            ->hideOnIndex()
            ->setHelp('输入框的占位提示文本')
        ;

        yield BooleanField::new('valid', '状态')
            ->setHelp('是否启用该字段')
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
        // 构建字段类型选项
        $fieldTypeChoices = [];
        foreach (FieldType::cases() as $case) {
            $fieldTypeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('model', '所属模型'))
            ->add(TextFilter::new('name', '字段名'))
            ->add(TextFilter::new('title', '显示名'))
            ->add(ChoiceFilter::new('type', '字段类型')->setChoices($fieldTypeChoices))
            ->add(BooleanFilter::new('required', '必填'))
            ->add(BooleanFilter::new('searchable', '可搜索'))
            ->add(BooleanFilter::new('importable', '支持导入'))
            ->add(BooleanFilter::new('valid', '状态'))
        ;
    }
}
