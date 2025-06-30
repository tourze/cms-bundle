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
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'cms_category', options: ['comment' => 'cms种类(类别)'])]
#[ORM\UniqueConstraint(name: 'cms_category_idx_uniq', columns: ['title', 'parent_id'])]
class Category implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use \Tourze\DoctrineUserBundle\Traits\BlameableAware;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY', inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $parent = null;

    /**
     * 下级分类列表.
     *
     * @var Collection<int, Category>
     */
    #[Groups(groups: ['restful_read', 'api_tree'])]
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'parent')]
    private Collection $children;

    #[Groups(groups: ['restful_read'])]
    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'categories')]
    private ?Model $model = null;

    /**
     * @var Collection<int, Entity>
     */
    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Entity::class, mappedBy: 'categories', fetch: 'EXTRA_LAZY')]
    private Collection $entities;

    #[Groups(groups: ['restful_read', 'api_tree'])]
    #[ORM\Column(type: Types::STRING, length: 60, options: ['comment' => '标题'])]
    private string $title = '';

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '缩略图'])]
    private ?string $thumb = null;

    /**
     * @var array<string, mixed>
     */
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => 'BANNER图'])]
    private array $banners = [];

    /**
     * @var array<string>
     */
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '热门关键词'])]
    private array $hotKeywords = [];

    #[IndexColumn]
    #[Groups(groups: ['admin_curd', 'api_tree', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    #[Assert\Type(type: ['numeric'])]
    private ?int $sortNumber = 0;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->entities = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
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

    /**
     * @return array<string, mixed>
     */
    public function getBanners(): array
    {
        return $this->banners;
    }

    /**
     * @param array<string, mixed>|null $banners
     */
    public function setBanners(?array $banners): self
    {
        $this->banners = $banners ?? [];

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getHotKeywords(): array
    {
        return $this->hotKeywords;
    }

    /**
     * @param array<string>|null $hotKeywords
     */
    public function setHotKeywords(?array $hotKeywords): self
    {
        $this->hotKeywords = $hotKeywords ?? [];

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
     * @return Collection<int, Category>
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

    /**
     * @return array<string, mixed>
     */
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

        if ($this->getChildren()->count() > 0) {
            foreach ($this->getChildren() as $child) {
                $res['children'][] = $child->getDataArray();
            }
        }

        if ($this->getModel() !== null) {
            $res['model'] = $this->getModel()->getDataArray();

            return $res;
        }

        return $res;
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveSortableArray(): array
    {
        return [
            'sortNumber' => $this->getSortNumber(),
        ];
    }
}
