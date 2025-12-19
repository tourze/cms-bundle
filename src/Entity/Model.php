<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Entity;

// use Tourze\CmsBundle\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\CmsBundle\Repository\ModelRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;
use Yiisoft\Arrays\ArraySorter;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Table(name: 'cms_model', options: ['comment' => '模型管理'])]
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[UniqueEntity(fields: ['code'], message: '模型代码已存在')]
class Model implements \Stringable, Itemable, AdminArrayInterface
{
    use BlameableAware;
    use IpTraceableAware;
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    public ?int $id = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '代号'])]
    private string $code;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '模型名'])]
    private string $title = '';

    /**
     * 关联属性.
     *
     * @var Collection<int, Attribute>
     */
    #[ORM\OneToMany(targetEntity: Attribute::class, mappedBy: 'model', indexBy: 'name')]
    private Collection $attributes;

    /**
     * 关联记录.
     *
     * @var Collection<int, Entity>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: Entity::class, mappedBy: 'model', orphanRemoval: true)]
    private Collection $entities;

    // /**
    //  * @var Collection<int, Category>
    //  */
    // #[Ignore]
    // #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'model', fetch: 'EXTRA_LAZY')]
    // private Collection $categories;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放点赞功能'])]
    private bool $allowLike = false;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放收藏功能'])]
    private bool $allowCollect = false;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否开放分享功能'])]
    private bool $allowShare = false;

    #[Groups(groups: ['restful_read'])]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '内容列表排序'])]
    private ?array $contentSorts = [];

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '专题列表排序'])]
    private ?array $topicSorts = [];

    #[TrackColumn]
    #[IndexColumn]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function __construct()
    {
        $this->entities = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        // $this->categories = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Collection<int, Entity>
     */
    public function getEntities(): Collection
    {
        return $this->entities;
    }

    public function addEntity(Entity $entity): void
    {
        if (!$this->entities->contains($entity)) {
            $this->entities->add($entity);
            $entity->setModel($this);
        }
    }

    public function removeEntity(Entity $entity): void
    {
        if ($this->entities->removeElement($entity)) {
            // set the owning side to null (unless already changed)
            if ($entity->getModel() === $this) {
                $entity->setModel(null);
            }
        }
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
            \SORT_DESC,
            \SORT_ASC,
        ]);

        // 确保返回连续索引数组，且元素类型正确
        return array_values(array_filter($attributes, fn ($item): bool => $item instanceof Attribute));
    }

    /**
     * @return Collection<int, Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): void
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setModel($this);
        }
    }

    public function removeAttribute(Attribute $attribute): void
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getModel() === $this) {
                $attribute->setModel(null);
            }
        }
    }

    public function getAllowLike(): ?bool
    {
        return $this->allowLike;
    }

    public function setAllowLike(bool $allowLike): void
    {
        $this->allowLike = $allowLike;
    }

    public function getAllowCollect(): ?bool
    {
        return $this->allowCollect;
    }

    public function setAllowCollect(bool $allowCollect): void
    {
        $this->allowCollect = $allowCollect;
    }

    public function getAllowShare(): ?bool
    {
        return $this->allowShare;
    }

    public function setAllowShare(bool $allowShare): void
    {
        $this->allowShare = $allowShare;
    }

    // /**
    //  * @return Collection<int, Category>
    //  */
    // public function getCategories(): Collection
    // {
    //     return $this->categories;
    // }

    // public function addCategory(Category $category): self
    // {
    //     if (!$this->categories->contains($category)) {
    //         $this->categories[] = $category;
    //         $category->setModel($this);
    //     }

    //     return $this;
    // }

    // public function removeCategory(Category $category): self
    // {
    //     if ($this->categories->removeElement($category)) {
    //         // set the owning side to null (unless already changed)
    //         if ($category->getModel() === $this) {
    //             $category->setModel(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
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
    public function setContentSorts(?array $contentSorts): void
    {
        $this->contentSorts = $contentSorts;
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
    public function setTopicSorts(?array $topicSorts): void
    {
        $this->topicSorts = $topicSorts;
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
            'name' => $this->getTitle(),
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
