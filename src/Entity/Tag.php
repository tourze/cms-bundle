<?php

namespace CmsBundle\Entity;

use CmsBundle\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'cms_tag', options: ['comment' => '内容标签表'])]
class Tag implements \Stringable
{
    use TimestampableAware;
    use \Tourze\DoctrineUserBundle\Traits\BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 60, unique: true, options: ['comment' => '标签名'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Entity>
     */
    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Entity::class, inversedBy: 'tags', fetch: 'EXTRA_LAZY')]
    private Collection $entities;

    #[ORM\ManyToOne(inversedBy: 'tags')]
    private ?TagGroup $groups = null;

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
    }

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        return $this->getName() ?? '';
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function renderEntityCount(): int
    {
        return $this->getEntities()->count();
    }

    public function getGroups(): ?TagGroup
    {
        return $this->groups;
    }

    public function setGroups(?TagGroup $groups): self
    {
        $this->groups = $groups;

        return $this;
    }}
