<?php

namespace CmsBundle\Entity;

use CmsBundle\Enum\FieldType;
use CmsBundle\Repository\AttributeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\EnumExtra\Selectable;

#[AsPermission(title: '字段属性')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Table(name: 'cms_attribute', options: ['comment' => 'cms属性表'])]
#[ORM\Entity(repositoryClass: AttributeRepository::class)]
class Attribute implements \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[CreatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Model $model = null;

    #[TrackColumn]
    #[Keyword]
    #[FormField(span: 8)]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '英文名'])]
    private ?string $name = null;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[Keyword]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '中文名'])]
    private ?string $title = null;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 32, enumType: FieldType::class, options: ['comment' => '数据类型'])]
    private FieldType $type;

    #[TrackColumn]
    #[FormField]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '默认值'])]
    private ?string $defaultValue = null;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[ListColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否必填'])]
    private ?bool $required = null;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '数据长度'])]
    private ?int $length = null;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 24, 'comment' => '编辑宽度'])]
    private ?int $span = null;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[ListColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否可搜索'])]
    private ?bool $searchable = false;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[ListColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '支持导入'])]
    private ?bool $importable = false;

    #[TrackColumn]
    #[FormField(span: 8)]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '排序'])]
    private ?int $displayOrder = 0;

    #[TrackColumn]
    #[FormField]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '配置'])]
    private ?string $config = null;

    #[FormField]
    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '占位提示文本'])]
    private ?string $placeholder = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getName()})";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): FieldType
    {
        return $this->type;
    }

    public function setType(FieldType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(?string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSpan(): ?int
    {
        return $this->span;
    }

    public function setSpan(int $span): self
    {
        $this->span = $span;

        return $this;
    }

    public function getSearchable(): ?bool
    {
        return $this->searchable;
    }

    public function setSearchable(bool $searchable): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    public function genSelectOptions(EntityManagerInterface $entityManager): array
    {
        if (!in_array($this->getType(), [FieldType::SINGLE_SELECT, FieldType::MULTIPLE_SELECT, FieldType::TAGS_SELECT])) {
            return [];
        }

        // 一般来说，单选的话，可能是sql也可能是枚举
        if (class_exists($this->config) && is_subclass_of($this->config, Selectable::class)) {
            $className = $this->config;
            $options = $className::genOptions();
        } elseif (str_starts_with($this->config, 'Entity') or str_starts_with($this->config, 'SELECT')) {
            // 起码要有查询 label/value
            if (str_starts_with($this->config, 'Entity')) {
                // Entity:cms_entity:label:value
                $arr = explode(':', $this->config);
                $sql = "select distinct {$arr[2]} as label,{$arr[3]} as id from {$arr[1]} order by id desc ";
            } else {
                $sql = $this->config;
            }

            $options = $entityManager->getConnection()->executeQuery($sql)->fetchAllAssociative();

            foreach ($options as $k => $v) {
                if (isset($v['label'])) {
                    $v['name'] = $v['label'];
                    $v['text'] = $v['label'];
                }

                if (isset($v['id'])) {
                    $v['value'] = $v['id'];
                }

                $options[$k] = $v;
            }
        } else {
            // 默认，就一行一个输入咯
            $lines = explode("\n", $this->config);
            $options = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (0 === strlen($line)) {
                    continue;
                }

                $options[] = [
                    'label' => $line,
                    'text' => $line,
                    'value' => $line,
                    'name' => $line,
                ];
            }
        }

        // 目前我们只可能使用string来控制枚举
        foreach ($options as $k => $v) {
            $v['value'] = strval($v['value']);
            $options[$k] = $v;
        }

        return $options;
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function setConfig(?string $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function isImportable(): ?bool
    {
        return $this->importable;
    }

    public function setImportable(?bool $importable): self
    {
        $this->importable = $importable;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(?string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }
}
