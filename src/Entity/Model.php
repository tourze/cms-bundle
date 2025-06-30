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
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\EnumExtra\Itemable;
use Yiisoft\Arrays\ArraySorter;

#[ORM\Table(name: 'cms_model', options: ['comment' => '模型管理'])]
#[ORM\Entity(repositoryClass: ModelRepository::class)]
class Model implements \Stringable, Itemable, AdminArrayInterface
{
    use TimestampableAware;
    use \Tourze\DoctrineUserBundle\Traits\BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '代号'])]
    private string $code;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '模型名'])]
    private string $title = '';

    /**
     * 关联属性.
     *
     * @var Collection<int, Attribute>
     */
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Attribute::class, indexBy: 'name')]
    private Collection $attributes;

    /**
     * 关联记录.
     *
     * @var Collection<int, Entity>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Entity::class, orphanRemoval: true)]
    private Collection $entities;

    /**
     * @var Collection<int, Category>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    private Collection $categories;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放点赞功能'])]
    private bool $allowLike = false;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放收藏功能'])]
    private bool $allowCollect = false;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放分享功能'])]
    private bool $allowShare = false;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '内容列表排序'])]
    private ?array $contentSorts = [];

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '专题列表排序'])]
    private ?array $topicSorts = [];

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

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
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        return "{$this->getTitle()}({$this->getCode()})";
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return array<int, Attribute>
     */
    public function getSortedAttributes(): array
    {
        /** @var Collection<int, Attribute> $collection */
        $collection = $this->getAttributes();
        /** @var array<int, Attribute> $attributes */
        $attributes = $collection->toArray();

        ArraySorter::multisort($attributes, [
            fn (Attribute $attribute) => $attribute->getDisplayOrder(),
            fn (Attribute $attribute) => $attribute->getId(),
        ], [
            SORT_DESC,
            SORT_ASC,
        ]);

        /** @phpstan-ignore-next-line */
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

    /**
     * @return array<string, mixed>|null
     */
    public function getContentSorts(): ?array
    {
        return $this->contentSorts;
    }

    /**
     * @param array<string, mixed>|null $contentSorts
     */
    public function setContentSorts(?array $contentSorts): self
    {
        $this->contentSorts = $contentSorts;

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getTopicSorts(): ?array
    {
        return $this->topicSorts;
    }

    /**
     * @param array<string, mixed>|null $topicSorts
     */
    public function setTopicSorts(?array $topicSorts): self
    {
        $this->topicSorts = $topicSorts;

        return $this;
    }

    public function renderEntityCount(): int
    {
        return $this->getEntities()->count();
    }

    /**
     * @return array<string, mixed>
     */
    public function toSelectItem(): array
    {
        return [
            'label' => $this->getTitle(),
            'text' => $this->getTitle(),
            'value' => $this->getId(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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
