<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CmsBundle\Enum\EntityState;
use Tourze\CmsBundle\Repository\EntityRepository;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\DoctrineUserBundle\Traits\CreateUserAware;
use Tourze\LockServiceBundle\Model\LockEntity;
use Tourze\TagManageBundle\Entity\Tag;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Table(name: 'cms_entity', options: ['comment' => '文章管理表'])]
#[ORM\Entity(repositoryClass: EntityRepository::class)]
class Entity implements \Stringable, AdminArrayInterface, LockEntity
{
    use BlameableAware;
    use CreateUserAware;
    use IpTraceableAware;
    use TimestampableAware;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '文章ID'])]
    public ?int $id = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '标题'])]
    private string $title;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\ManyToOne(targetEntity: Model::class, fetch: 'EXTRA_LAZY', inversedBy: 'entities')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Model $model = null;

    /**
     * @var Collection<int, Catalog>
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\ManyToMany(targetEntity: Catalog::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'cms_entity_catalog')]
    private Collection $catalogs;

    /**
     * @var Collection<int, Tag>
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'entities', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'cms_entity_tag')]
    private Collection $tags;

    /**
     * @var Collection<int, Value>
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\OneToMany(targetEntity: Value::class, mappedBy: 'entity', fetch: 'EXTRA_LAZY', orphanRemoval: true, indexBy: 'attribute_id')]
    private Collection $valueList;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发布时间'])]
    private ?\DateTimeImmutable $publishTime = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeImmutable $endTime = null;

    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [EntityState::class, 'cases'])]
    #[ORM\Column(type: Types::STRING, length: 32, enumType: EntityState::class, options: ['comment' => '状态'])]
    private EntityState $state;

    #[Groups(groups: ['admin_curd'])]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    #[Groups(groups: ['admin_curd'])]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    public function __construct()
    {
        $this->catalogs = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->valueList = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId() || 0 === $this->getId()) {
            return '';
        }

        return "{$this->getId()}:{$this->getTitle()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): void
    {
        $this->model = $model;
    }

    public function getState(): EntityState
    {
        return $this->state;
    }

    public function setState(EntityState $state): void
    {
        $this->state = $state;
    }

    /**
     * @return Collection<int, Catalog>
     */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    public function addCatalog(Catalog $catalog): void
    {
        if (!$this->catalogs->contains($catalog)) {
            $this->catalogs->add($catalog);
        }
    }

    public function removeCatalog(Catalog $catalog): void
    {
        $this->catalogs->removeElement($catalog);
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return Collection<int, Value>
     */
    public function getValueList(): Collection
    {
        return $this->valueList;
    }

    public function addValueList(Value $valueList): void
    {
        if (!$this->valueList->contains($valueList)) {
            $this->valueList->add($valueList);
            $valueList->setEntity($this);
        }
    }

    public function removeValueList(Value $valueList): void
    {
        if ($this->valueList->removeElement($valueList)) {
            // set the owning side to null (unless already changed)
            if ($valueList->getEntity() === $this) {
                $valueList->setEntity(null);
            }
        }
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    /**
     * 方便读取数据.
     *
     * @return array<string, mixed>
     */
    #[Groups(groups: ['restful_read'])]
    public function getValues(): array
    {
        $result = [];
        foreach ($this->getValueList() as $item) {
            if (null !== $item->getAttribute()) {
                $result[$item->getAttribute()->getName()] = $item->getCastData();
            }
        }

        return $result;
    }

    /**
     * 获取产品型号（虚拟方法，用于EasyAdmin字段映射）.
     */
    public function getProductType(): ?string
    {
        $values = $this->getValues();
        $productType = $values['product_type'] ?? null;

        return \is_string($productType) ? $productType : null;
    }

    /**
     * 获取内容（虚拟方法，用于EasyAdmin字段映射）.
     */
    public function getContent(): ?string
    {
        $values = $this->getValues();
        $content = $values['content'] ?? null;

        return \is_string($content) ? $content : null;
    }

    public function getPublishTime(): ?\DateTimeImmutable
    {
        return $this->publishTime;
    }

    public function setPublishTime(?\DateTimeImmutable $publishTime): void
    {
        $this->publishTime = $publishTime;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeImmutable $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'title' => $this->getTitle(),
            'publishTime' => $this->getPublishTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'state' => $this->getState(),
            'sortNumber' => $this->getSortNumber(),
        ];
    }

    public function retrieveLockResource(): string
    {
        return "cms_entity_{$this->getId()}";
    }
}
