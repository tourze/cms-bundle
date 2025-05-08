<?php

namespace CmsBundle\Entity;

use CmsBundle\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
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
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Column\TreeView;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[Deletable]
#[Editable]
#[Creatable]
#[AsPermission(title: '目录')]
#[TreeView(dataModel: Category::class, targetAttribute: 'parent')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'cms_category', options: ['comment' => 'cms种类(类别)'])]
#[ORM\UniqueConstraint(name: 'cms_category_idx_uniq', columns: ['title', 'parent_id'])]
class Category implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY', inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $parent = null;

    /**
     * 下级分类列表.
     *
     * @var Collection<Category>
     */
    #[Groups(['restful_read', 'api_tree'])]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class)]
    private Collection $children;

    #[FormField(title: '模型', span: 12)]
    #[ListColumn(title: '模型')]
    #[Groups(['restful_read'])]
    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'categories')]
    private ?Model $model = null;

    /**
     * @var Collection<Entity>
     */
    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Entity::class, mappedBy: 'categories', fetch: 'EXTRA_LAZY')]
    private Collection $entities;

    #[FormField(span: 12)]
    #[Filterable]
    #[ListColumn]
    #[Groups(['restful_read', 'api_tree'])]
    #[ORM\Column(type: Types::STRING, length: 60, options: ['comment' => '标题'])]
    private ?string $title = '';

    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[FormField]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '缩略图'])]
    private ?string $thumb = null;

    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => 'BANNER图'])]
    private array $banners = [];

    #[FormField]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '热门关键词'])]
    private array $hotKeywords = [];

    /**
     * order值大的排序靠前。有效的值范围是[0, 2^32].
     */
    #[IndexColumn]
    #[Groups(['admin_curd', 'api_tree', 'restful_read', 'restful_write'])]
    #[FormField]
    #[ListColumn(order: 95, sorter: true)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    #[Assert\Type(type: ['numeric'])]
    private ?int $sortNumber = 0;

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

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->entities = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getId()})";
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

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

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

    /**
     * @return Collection<int, Entity>
     */
    public function getEntities(): Collection
    {
        return $this->entities;
    }

    public function addEntity(Entity $entity): self
    {
        if (!$this->entities->contains($entity)) {
            $this->entities[] = $entity;
            $entity->addCategory($this);
        }

        return $this;
    }

    public function removeEntity(Entity $entity): self
    {
        if ($this->entities->removeElement($entity)) {
            $entity->removeCategory($this);
        }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function setThumb(?string $thumb): self
    {
        $this->thumb = $thumb;

        return $this;
    }

    public function getBanners(): ?array
    {
        return $this->banners;
    }

    public function setBanners(?array $banners): self
    {
        $this->banners = $banners;

        return $this;
    }

    public function getHotKeywords(): ?array
    {
        return $this->hotKeywords;
    }

    public function setHotKeywords(?array $hotKeywords): self
    {
        $this->hotKeywords = $hotKeywords;

        return $this;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function allowContribute(): bool
    {
        return false;
    }

    public function getDataArray(): array
    {
        $res = [
            'id' => $this->getId(),
            'model' => [],
            'children' => [],
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'thumb' => $this->getThumb(),
            'banners' => $this->getBanners(),
            'hotKeywords' => $this->getHotKeywords(),
            ...$this->retrieveSortableArray(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];

        if ($this->getChildren()) {
            foreach ($this->getChildren() as $child) {
                $res['children'][] = $child->getDataArray();
            }
        }

        if ($this->getModel()) {
            $res['model'] = $this->getModel()->getDataArray();

            return $res;
        }

        return $res;
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'thumb' => $this->getThumb(),
            'banners' => $this->getBanners(),
            'hotKeywords' => $this->getHotKeywords(),
            ...$this->retrieveSortableArray(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'thumb' => $this->getThumb(),
            'banners' => $this->getBanners(),
            'hotKeywords' => $this->getHotKeywords(),
            ...$this->retrieveSortableArray(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveAdminTreeArray(): array
    {
        $children = null;
        if ($this->getChildren()->count() > 0) {
            $children = [];
            foreach ($this->getChildren() as $child) {
                $children[] = $child->retrieveAdminTreeArray();
            }
        }

        return [
            ...$this->retrieveAdminArray(),
            'children' => $children,
        ];
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

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function retrieveSortableArray(): array
    {
        return [
            'sortNumber' => $this->getSortNumber(),
        ];
    }
}
