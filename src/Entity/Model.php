<?php

namespace CmsBundle\Entity;

use CmsBundle\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\CopyColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\EnumExtra\Itemable;
use Yiisoft\Arrays\ArraySorter;

#[AsPermission(title: '模型管理')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Table(name: 'cms_model', options: ['comment' => '模型管理表'])]
#[ORM\Entity(repositoryClass: ModelRepository::class)]
class Model implements \Stringable, Itemable, AdminArrayInterface
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[FormField(span: 8)]
    #[Keyword]
    #[CopyColumn(suffix: true)]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '代号'])]
    private string $code;

    #[FormField(span: 8)]
    #[Keyword]
    #[CopyColumn(suffix: true)]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '模型名'])]
    private string $title = '';

    /**
     * 关联属性.
     *
     * @var Collection<Attribute>
     */
    #[CopyColumn(suffix: true)]
    #[ListColumn(title: '属性')]
    #[CurdAction(label: '属性', drawerWidth: 1200)]
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Attribute::class, indexBy: 'name')]
    private Collection $attributes;

    /**
     * 关联记录.
     *
     * @var Collection<Entity>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Entity::class, orphanRemoval: true)]
    private Collection $entities;

    /**
     * @var Collection<Category>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    private Collection $categories;

    #[FormField(span: 8)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放点赞功能'])]
    private bool $allowLike = false;

    #[FormField(span: 8)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放收藏功能'])]
    private bool $allowCollect = false;

    #[FormField(span: 8)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放分享功能'])]
    private bool $allowShare = false;

    #[FormField(span: 6)]
    #[ListColumn(sorter: true)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * TODO 怎么做这种枚举的下拉选择？
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '内容列表排序'])]
    private ?array $contentSorts = [];

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '专题列表排序'])]
    private ?array $topicSorts = [];

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->entities = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getCode()})";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<Entity>
     */
    public function getEntities(): Collection
    {
        return $this->entities;
    }

    public function addEntity(Entity $entity): self
    {
        if (!$this->entities->contains($entity)) {
            $this->entities[] = $entity;
            $entity->setModel($this);
        }

        return $this;
    }

    public function removeEntity(Entity $entity): self
    {
        if ($this->entities->removeElement($entity)) {
            // set the owning side to null (unless already changed)
            if ($entity->getModel() === $this) {
                $entity->setModel(null);
            }
        }

        return $this;
    }

    /**
     * @return array|Attribute[]
     */
    public function getSortedAttributes(): array
    {
        $attributes = $this->getAttributes()
            // ->filter(fn (Attribute $attribute) => (bool) $attribute->isValid())
            ->toArray();

        ArraySorter::multisort($attributes, [
            fn (Attribute $attribute) => $attribute->getDisplayOrder(),
            fn (Attribute $attribute) => $attribute->getId(),
        ], [
            SORT_DESC,
            SORT_ASC,
        ]);

        return $attributes;
    }

    /**
     * @return Collection<int, Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setModel($this);
        }

        return $this;
    }

    public function removeAttribute(Attribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getModel() === $this) {
                $attribute->setModel(null);
            }
        }

        return $this;
    }

    public function getAllowLike(): ?bool
    {
        return $this->allowLike;
    }

    public function setAllowLike(bool $allowLike): self
    {
        $this->allowLike = $allowLike;

        return $this;
    }

    public function getAllowCollect(): ?bool
    {
        return $this->allowCollect;
    }

    public function setAllowCollect(bool $allowCollect): self
    {
        $this->allowCollect = $allowCollect;

        return $this;
    }

    public function getAllowShare(): ?bool
    {
        return $this->allowShare;
    }

    public function setAllowShare(bool $allowShare): self
    {
        $this->allowShare = $allowShare;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setModel($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getModel() === $this) {
                $category->setModel(null);
            }
        }

        return $this;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function getContentSorts(): ?array
    {
        return $this->contentSorts;
    }

    public function setContentSorts(?array $contentSorts): self
    {
        $this->contentSorts = $contentSorts;

        return $this;
    }

    public function getTopicSorts(): ?array
    {
        return $this->topicSorts;
    }

    public function setTopicSorts(?array $topicSorts): self
    {
        $this->topicSorts = $topicSorts;

        return $this;
    }

    #[ListColumn(title: '文章数')]
    public function renderEntityCount(): int
    {
        return $this->getEntities()->count();
    }

    public function toSelectItem(): array
    {
        return [
            'label' => $this->getTitle(),
            'text' => $this->getTitle(),
            'value' => $this->getId(),
        ];
    }

    public function getDataArray(): array
    {
        return [
            'code' => $this->getCode(),
            'title' => $this->getTitle(),
            'allowLike' => $this->getAllowLike(),
            'sortNumber' => $this->getSortNumber(),
            'id' => $this->getId(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return [
            'code' => $this->getCode(),
            'title' => $this->getTitle(),
            'allowLike' => $this->getAllowLike(),
            'sortNumber' => $this->getSortNumber(),
            'id' => $this->getId(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
