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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Tourze\CmsBundle\Entity\Value;
use Tourze\CmsBundle\Enum\FieldType;

/**
 * CMS数据值管理控制器.
 *
 * @template TEntity of Value
 *
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(routePath: '/cms/value', routeName: 'cms_value')]
final class ValueCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    /**
     * @return class-string<Value>
     */
    public static function getEntityFqcn(): string
    {
        return Value::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('数据值')
            ->setEntityLabelInPlural('数据值管理')
            ->setPageTitle('index', '数据值列表')
            ->setPageTitle('new', '新建数据值')
            ->setPageTitle('edit', '编辑数据值')
            ->setPageTitle('detail', '数据值详情')
            ->setHelp('index', '管理CMS实体的自定义字段数据值，支持多种数据类型的存储和转换')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['data'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setMaxLength(9999)
        ;

        yield $this->createEntityField();

        yield $this->createAttributeField();

        yield $this->createModelField();

        yield $this->createAttributeTypeField();

        yield $this->createDataField();

        yield $this->createCastDataField();

        yield $this->createRawDataField();

        yield from $this->createAuditFields();
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewEntityAction = $this->createViewEntityAction();
        $viewAttributeAction = $this->createViewAttributeAction();
        $manageEntityValuesAction = $this->createManageEntityValuesAction();

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $viewEntityAction)
            ->add(Crud::PAGE_DETAIL, $viewEntityAction)
            ->add(Crud::PAGE_DETAIL, $viewAttributeAction)
            ->add(Crud::PAGE_DETAIL, $manageEntityValuesAction)
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('entity', '所属实体'))
            ->add(EntityFilter::new('attribute', '对应属性'))
            ->add(EntityFilter::new('model', '所属模型'))
            ->add(TextFilter::new('data', '数据内容'))
        ;
    }

    /**
     * 格式化值数据显示.
     */
    private function formatValueData(mixed $value, mixed $entity): string
    {
        if (!($entity instanceof Value) || null === $value || '' === $value) {
            return \is_scalar($value) ? (string) $value : '';
        }

        $attribute = $entity->getAttribute();
        if (null === $attribute || null === $attribute->getType()) {
            return \is_scalar($value) ? (string) $value : '';
        }

        return $this->formatImageTypeValue($value, $attribute->getType());
    }

    /**
     * 格式化图片类型的值.
     */
    private function formatImageTypeValue(mixed $value, FieldType $type): string
    {
        if (FieldType::SINGLE_IMAGE === $type || FieldType::MULTIPLE_IMAGE === $type) {
            return $this->formatJsonValue($value);
        }

        return \is_scalar($value) ? (string) $value : '';
    }

    /**
     * 格式化JSON值.
     */
    private function formatJsonValue(mixed $value): string
    {
        if (!\is_string($value)) {
            return \is_scalar($value) ? (string) $value : '';
        }

        $decoded = json_decode($value, true);
        if (!\is_array($decoded)) {
            return $value;
        }

        $result = json_encode($decoded, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE);

        return false !== $result ? $result : $value;
    }

    /**
     * 创建实体字段配置.
     */
    private function createEntityField(): AssociationField
    {
        return AssociationField::new('entity', '所属实体')
            ->setRequired(true)
            ->setCrudController(EntityCrudController::class)
            ->autocomplete()
            ->setHelp('数据值所属的内容实体')
            ->formatValue(function ($value) {
                if (\is_object($value) && method_exists($value, 'getId') && method_exists($value, 'getTitle')) {
                    $id = $value->getId();
                    $title = $value->getTitle();

                    return \sprintf('%s: %s',
                        \is_scalar($id) ? (string) $id : '',
                        \is_scalar($title) ? (string) $title : ''
                    );
                }

                return '';
            });
    }

    /**
     * 创建属性字段配置.
     */
    private function createAttributeField(): AssociationField
    {
        return AssociationField::new('attribute', '对应属性')
            ->setRequired(true)
            ->setCrudController(AttributeCrudController::class)
            ->autocomplete()
            ->setHelp('数据值对应的属性字段')
            ->formatValue(fn ($value) => $this->formatAttributeValue($value));
    }

    /**
     * 格式化属性值显示.
     */
    private function formatAttributeValue(mixed $value): string
    {
        if (!\is_object($value) || !method_exists($value, 'getTitle') || !method_exists($value, 'getName') || !method_exists($value, 'getType')) {
            return '';
        }

        $type = $value->getType();
        $title = $value->getTitle();
        $name = $value->getName();
        $typeLabel = $this->getTypeLabel($type);

        return \sprintf('%s (%s) - %s',
            \is_scalar($title) ? (string) $title : '',
            \is_scalar($name) ? (string) $name : '',
            $typeLabel
        );
    }

    /**
     * 获取类型标签.
     */
    private function getTypeLabel(mixed $type): string
    {
        if (\is_object($type) && method_exists($type, 'getLabel')) {
            $label = $type->getLabel();

            return \is_scalar($label) ? (string) $label : '未知类型';
        }

        return '未知类型';
    }

    /**
     * 创建模型字段配置.
     */
    private function createModelField(): AssociationField
    {
        return AssociationField::new('model', '所属模型')
            ->hideOnForm()
            ->setHelp('数据值所属的模型（从属性继承）')
            ->formatValue(function ($value) {
                if (\is_object($value) && method_exists($value, 'getTitle') && method_exists($value, 'getCode')) {
                    $title = $value->getTitle();
                    $code = $value->getCode();

                    return \sprintf('%s (%s)',
                        \is_scalar($title) ? (string) $title : '',
                        \is_scalar($code) ? (string) $code : ''
                    );
                }

                return '';
            });
    }

    /**
     * 创建属性类型字段配置.
     */
    private function createAttributeTypeField(): TextField
    {
        return TextField::new('attributeType', '字段类型')
            ->onlyOnDetail()
            ->setHelp('该数据值对应的属性字段类型')
            ->formatValue(function ($value, $entity) {
                if ($entity instanceof Value) {
                    $attribute = $entity->getAttribute();
                    if (null !== $attribute && null !== $attribute->getType()) {
                        return $attribute->getType()->getLabel();
                    }
                }

                return '未知类型';
            });
    }

    /**
     * 创建数据字段配置.
     */
    private function createDataField(): TextareaField
    {
        return TextareaField::new('data', '数据内容')
            ->setHelp('原始数据内容，根据属性类型自动转换')
            ->setNumOfRows(4)
            ->hideOnIndex()
            ->formatValue(function ($value, $entity) {
                return $this->formatValueData($value, $entity);
            });
    }

    /**
     * 创建转换后数据字段配置.
     */
    private function createCastDataField(): TextField
    {
        return TextField::new('castData', '转换后数据')
            ->onlyOnDetail()
            ->setHelp('根据属性类型转换后的数据值')
            ->formatValue(function ($value, $entity) {
                return $this->formatCastData($entity);
            });
    }

    /**
     * 创建原始数据字段配置.
     *
     * 使用虚拟字段名 rawDataJson 避免 EasyAdmin 尝试将数组类型的 rawData 属性直接转换为字符串
     */
    private function createRawDataField(): TextField
    {
        return TextField::new('rawDataJson', '原始数据数组')
            ->onlyOnDetail()
            ->setHelp('原始数据的数组形式')
            ->setVirtual(true)
            ->formatValue(fn ($value, $entity) => $entity instanceof Value
                ? $this->encodeJsonPretty($entity->getRawData())
                : '[]');
    }

    /**
     * 创建审计字段配置.
     *
     * @return iterable<TextField|DateTimeField>
     */
    private function createAuditFields(): iterable
    {
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');

        yield TextField::new('createdBy', '创建人')
            ->onlyOnDetail();

        yield TextField::new('updatedBy', '更新人')
            ->onlyOnDetail();
    }

    /**
     * 创建查看实体操作.
     */
    private function createViewEntityAction(): Action
    {
        return Action::new('viewEntity', '查看实体', 'fa fa-eye')
            ->linkToUrl(function (Value $entity) {
                $relatedEntity = $entity->getEntity();
                if (null !== $relatedEntity) {
                    return $this->adminUrlGenerator
                        ->setController(EntityCrudController::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($relatedEntity->getId())
                        ->generateUrl();
                }

                return '#';
            })
            ->setHtmlAttributes(['title' => '查看该数据值所属的实体详情'])
            ->displayIf(fn (Value $entity) => null !== $entity->getEntity());
    }

    /**
     * 创建查看属性操作.
     */
    private function createViewAttributeAction(): Action
    {
        return Action::new('viewAttribute', '查看属性', 'fa fa-cog')
            ->linkToUrl(function (Value $entity) {
                $attribute = $entity->getAttribute();
                if (null !== $attribute) {
                    return $this->adminUrlGenerator
                        ->setController(AttributeCrudController::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($attribute->getId())
                        ->generateUrl();
                }

                return '#';
            })
            ->setHtmlAttributes(['title' => '查看该数据值对应的属性定义'])
            ->displayIf(fn (Value $entity) => null !== $entity->getAttribute());
    }

    /**
     * 创建管理实体数据操作.
     */
    private function createManageEntityValuesAction(): Action
    {
        return Action::new('manageEntityValues', '管理实体数据', 'fa fa-list')
            ->linkToUrl(function (Value $entity) {
                $relatedEntity = $entity->getEntity();
                if (null !== $relatedEntity) {
                    return $this->adminUrlGenerator
                        ->setController(ValueCrudController::class)
                        ->setAction(Action::INDEX)
                        ->set('filters[entity][comparison]', '=')
                        ->set('filters[entity][value]', $relatedEntity->getId())
                        ->generateUrl();
                }

                return '#';
            })
            ->setHtmlAttributes(['title' => '管理同一实体的所有数据值'])
            ->displayIf(fn (Value $entity) => null !== $entity->getEntity());
    }

    /**
     * 格式化转换后的数据显示.
     */
    private function formatCastData(mixed $entity): string
    {
        if (!($entity instanceof Value)) {
            return '';
        }

        $castData = $entity->getCastData();
        if (\is_array($castData)) {
            return $this->encodeJsonPretty($castData);
        }

        if (\is_object($castData)) {
            return 'Object';
        }

        return \is_scalar($castData) ? (string) $castData : '';
    }

    /**
     * 将数组编码为格式化的 JSON 字符串.
     *
     * @param array<mixed> $data
     */
    private function encodeJsonPretty(array $data): string
    {
        $json = json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE);

        return false !== $json ? $json : '[]';
    }
}
