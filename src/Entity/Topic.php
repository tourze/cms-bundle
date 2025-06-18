<?php

namespace CmsBundle\Entity;

use CmsBundle\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;

#[Listable]
#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\Table(name: 'cms_topic', options: ['comment' => '内容专题表'])]
class Topic implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '名称'])]
    private ?string $title = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '缩略图'])]
    private ?string $thumb = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => 'BANNER'])]
    private array $banners = [];

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否推荐'])]
    private ?bool $recommend = null;

    /**
     * @var Collection<Entity>
     */
    #[ORM\ManyToMany(targetEntity: Entity::class, inversedBy: 'topics', fetch: 'EXTRA_LAZY')]
    private Collection $entities;

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
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return $this->getTitle();
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

    public function getTitle(): ?string
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

    public function getRecommend(): ?bool
    {
        return $this->recommend;
    }

    public function setRecommend(bool $recommend): self
    {
        $this->recommend = $recommend;

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
        }

        return $this;
    }

    public function removeEntity(Entity $entity): self
    {
        $this->entities->removeElement($entity);

        return $this;
    }

    public function getEntityCount(): int
    {
        return $this->getEntities()->count();
    }}
