<?php

declare(strict_types=1);

namespace CmsBundle\Controller\Admin;

use CmsBundle\Entity\Model;
use CmsBundle\Enum\ContentSort;
use CmsBundle\Enum\TopicSort;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

/**
 * 模型管理控制器.
 *
 * @template TEntity of Model
 *
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(routePath: '/cms/model', routeName: 'cms_model')]
final class ModelCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    /**
     * @return class-string<Model>
     */
    public static function getEntityFqcn(): string
    {
        return Model::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('模型')
            ->setEntityLabelInPlural('模型管理')
            ->setPageTitle('index', '模型列表')
            ->setPageTitle('new', '新建模型')
            ->setPageTitle('edit', '编辑模型')
            ->setPageTitle('detail', '模型详情')
            ->setHelp('index', '管理内容模型的结构定义和配置规则')
            ->setDefaultSort(['sortNumber' => 'DESC', 'id' => 'DESC'])
            ->setSearchFields(['code', 'title'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setMaxLength(9999)
        ;

        yield TextField::new('code', '模型代码')
            ->setHelp('唯一标识符，用于系统内部调用，不可重复')
            ->setRequired(true)
        ;

        yield TextField::new('title', '模型名称')
            ->setHelp('模型的显示名称')
            ->setRequired(true)
        ;

        yield IntegerField::new('sortNumber', '排序编号')
            ->setHelp('数值越大排序越靠前')
            ->hideOnIndex()
        ;

        // 功能开关字段
        yield BooleanField::new('allowLike', '允许点赞')
            ->setHelp('是否启用点赞功能')
        ;

        yield BooleanField::new('allowCollect', '允许收藏')
            ->setHelp('是否启用收藏功能')
        ;

        yield BooleanField::new('allowShare', '允许分享')
            ->setHelp('是否启用分享功能')
        ;

        // 排序配置字段
        yield ChoiceField::new('contentSorts', '内容排序规则')
            ->setChoices($this->getContentSortChoices())
            ->allowMultipleChoices()
            ->renderExpanded(false)
            ->setHelp('内容列表的排序方式，支持多选')
            ->hideOnIndex()
        ;

        yield ChoiceField::new('topicSorts', '专题排序规则')
            ->setChoices($this->getTopicSortChoices())
            ->allowMultipleChoices()
            ->renderExpanded(false)
            ->setHelp('专题列表的排序方式，支持多选')
            ->hideOnIndex()
        ;

        yield BooleanField::new('valid', '状态')
            ->setHelp('是否启用该模型')
        ;

        // 统计信息字段
        yield IntegerField::new('renderEntityCount', '内容数量')
            ->formatValue(function ($value) {
                return number_format(is_numeric($value) ? (int) $value : 0);
            })
            ->onlyOnDetail()
        ;

        yield IntegerField::new('renderAttributeCount', '属性数量')
            ->formatValue(function ($value, $entity) {
                if ($entity instanceof Model) {
                    return number_format($entity->getAttributes()->count());
                }

                return '0';
            })
            ->onlyOnDetail()
        ;

        // 属性列表字段（仅在详情页显示）
        yield CollectionField::new('attributes', '模型属性')
            ->onlyOnDetail()
            ->setHelp('该模型定义的所有属性字段，点击"管理属性"按钮进行详细管理')
            ->formatValue(function ($value, $entity) {
                if ($entity instanceof Model) {
                    $attributes = $entity->getAttributes();
                    $list = [];
                    foreach ($attributes as $attribute) {
                        $list[] = \sprintf('%s (%s) - %s',
                            $attribute->getTitle(),
                            $attribute->getName(),
                            $attribute->getType()?->getLabel() ?? '未知类型'
                        );
                    }

                    return [] !== $list ? implode('<br>', $list) : '暂无属性';
                }

                return '暂无属性';
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
        // 创建管理属性的自定义操作
        $manageAttributesAction = Action::new('manageAttributes', '管理属性', 'fa fa-list')
            ->linkToUrl(function (Model $entity) {
                return $this->adminUrlGenerator
                    ->setController(AttributeCrudController::class)
                    ->setAction(Action::INDEX)
                    ->set('filters[model][comparison]', '=')
                    ->set('filters[model][value]', $entity->getId())
                    ->generateUrl()
                ;
            })
            ->setHtmlAttributes(['title' => '管理该模型的所有属性字段'])
        ;

        // 创建新属性的操作
        $newAttributeAction = Action::new('newAttribute', '新建属性', 'fa fa-plus')
            ->linkToUrl(function (Model $entity) {
                return $this->adminUrlGenerator
                    ->setController(AttributeCrudController::class)
                    ->setAction(Action::NEW)
                    ->set('model', $entity->getId())
                    ->generateUrl()
                ;
            })
            ->setHtmlAttributes(['title' => '为该模型新建属性字段'])
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $manageAttributesAction)
            ->add(Crud::PAGE_DETAIL, $manageAttributesAction)
            ->add(Crud::PAGE_DETAIL, $newAttributeAction)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, 'manageAttributes'])
            ->reorder(Crud::PAGE_DETAIL, ['manageAttributes', 'newAttribute'])
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('code', '模型代码'))
            ->add(TextFilter::new('title', '模型名称'))
            ->add(BooleanFilter::new('allowLike', '允许点赞'))
            ->add(BooleanFilter::new('allowCollect', '允许收藏'))
            ->add(BooleanFilter::new('allowShare', '允许分享'))
            ->add(BooleanFilter::new('valid', '状态'))
        ;
    }

    /**
     * 处理实体持久化异常.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try {
            parent::persistEntity($entityManager, $entityInstance);
        } catch (UniqueConstraintViolationException $e) {
            // 处理唯一约束违反异常
            $this->addFlash('danger', '模型代码已存在，请使用其他代码');
            throw $e;
        } catch (\Exception $e) {
            // 处理其他异常
            $this->addFlash('danger', '保存模型时发生错误：'.$e->getMessage());
            throw $e;
        }
    }

    /**
     * 获取内容排序选项.
     *
     * @return array<string, string>
     */
    private function getContentSortChoices(): array
    {
        $choices = [];
        foreach (ContentSort::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $choices;
    }

    /**
     * 获取专题排序选项.
     *
     * @return array<string, string>
     */
    private function getTopicSortChoices(): array
    {
        $choices = [];
        foreach (TopicSort::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $choices;
    }
}
