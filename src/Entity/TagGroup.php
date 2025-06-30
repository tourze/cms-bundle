<?php

namespace CmsBundle\Entity;

use CmsBundle\Repository\TagGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: TagGroupRepository::class)]
#[ORM\Table(name: 'cms_tag_group', options: ['comment' => '标签分组表'])]
class TagGroup implements \Stringable
{
    use TimestampableAware;
    use \Tourze\DoctrineUserBundle\Traits\BlameableAware;
    use SnowflakeKeyAware;

    #[ORM\Column(length: 60, options: ['comment' => '组名'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\OneToMany(mappedBy: 'groups', targetEntity: Tag::class)]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->getId() === null) {
            return '';
        }

        return $this->getName() ?? '';
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
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->setGroups($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            // set the owning side to null (unless already changed)
            if ($tag->getGroups() === $this) {
                $tag->setGroups(null);
            }
        }

        return $this;
    }}
