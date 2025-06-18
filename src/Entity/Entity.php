<?php

namespace CmsBundle\Entity;

use CmsBundle\Enum\EntityState;
use CmsBundle\Repository\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\BatchDeletable;
use Tourze\EasyAdmin\Attribute\Action\Importable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\ImportColumn;
use Tourze\EasyAdmin\Attribute\Field\LinkageField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\LockServiceBundle\Model\LockEntity;

#[Listable(actionWidth: 150)]
#[Importable(generateTemplate: false, featureKey: 'CMS_ENTITY_IMPORTABLE')]
#[BatchDeletable]
#[ORM\Table(name: 'cms_entity', options: ['comment' => '文章管理表'])]
#[ORM\Entity(repositoryClass: EntityRepository::class)]
class Entity implements \Stringable, AdminArrayInterface, LockEntity
{
    use TimestampableAware;
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '文章ID'])]
    private ?int $id = 0;

    #[ImportColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '标题'])]
    private string $title;

    #[LinkageField]
    #[Filterable(label: '模型')]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\ManyToOne(targetEntity: Model::class, fetch: 'EXTRA_LAZY', inversedBy: 'entities')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Model $model = null;

    #[Filterable(label: '目录')]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'entities', fetch: 'EXTRA_LAZY')]
    private Collection $categories;

    /**
     * @var Collection<Tag>
     */
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'entities', fetch: 'EXTRA_LAZY')]
    private Collection $tags;

    /**
     * @var Collection<Topic>
     */
    #[Groups(['admin_curd'])]
    #[ORM\ManyToMany(targetEntity: Topic::class, mappedBy: 'entities', fetch: 'EXTRA_LAZY')]
    private Collection $topics;

    /**
     * @var Collection<Value>
     */
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\OneToMany(mappedBy: 'entity', targetEntity: Value::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true, indexBy: 'attribute_id')]
    private Collection $valueList;

    #[ImportColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '发布时间'])]
    private ?\DateTimeInterface $publishTime = null;

    #[ImportColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    #[ImportColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 32, enumType: EntityState::class, options: ['comment' => '状态'])]
    private EntityState $state;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * @var Collection<CollectLog>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'entity', targetEntity: CollectLog::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $collectLogs;

    /**
     * @var Collection<LikeLog>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'entity', targetEntity: LikeLog::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $likeLogs;

    /**
     * @var Collection<ShareLog>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'entity', targetEntity: ShareLog::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $shareLogs;

    #[ImportColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    /**
     * @var Collection<int, Comment>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'entity', targetEntity: Comment::class)]
    private Collection $comments;

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
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->topics = new ArrayCollection();
        $this->valueList = new ArrayCollection();
        $this->collectLogs = new ArrayCollection();
        $this->likeLogs = new ArrayCollection();
        $this->shareLogs = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function __toString()
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getId()}:{$this->getTitle()}";
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getState(): EntityState
    {
        return $this->state;
    }

    public function setState(EntityState $state): self
    {
        $this->state = $state;

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
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

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
            $this->tags[] = $tag;
            $tag->addEntity($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeEntity($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->addEntity($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            $topic->removeEntity($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Value>
     */
    public function getValueList(): Collection
    {
        return $this->valueList;
    }

    public function addValueList(Value $valueList): self
    {
        if (!$this->valueList->contains($valueList)) {
            $this->valueList[] = $valueList;
            $valueList->setEntity($this);
        }

        return $this;
    }

    public function removeValueList(Value $valueList): self
    {
        if ($this->valueList->removeElement($valueList)) {
            // set the owning side to null (unless already changed)
            if ($valueList->getEntity() === $this) {
                $valueList->setEntity(null);
            }
        }

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

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
     * @return Collection<int, CollectLog>
     */
    public function getCollectLogs(): Collection
    {
        return $this->collectLogs;
    }

    public function addCollectLog(CollectLog $collectLog): self
    {
        if (!$this->collectLogs->contains($collectLog)) {
            $this->collectLogs[] = $collectLog;
            $collectLog->setEntity($this);
        }

        return $this;
    }

    public function removeCollectLog(CollectLog $collectLog): self
    {
        if ($this->collectLogs->removeElement($collectLog)) {
            // set the owning side to null (unless already changed)
            if ($collectLog->getEntity() === $this) {
                $collectLog->setEntity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LikeLog>
     */
    public function getLikeLogs(): Collection
    {
        return $this->likeLogs;
    }

    public function addLikeLog(LikeLog $likeLog): self
    {
        if (!$this->likeLogs->contains($likeLog)) {
            $this->likeLogs[] = $likeLog;
            $likeLog->setEntity($this);
        }

        return $this;
    }

    public function removeLikeLog(LikeLog $likeLog): self
    {
        if ($this->likeLogs->removeElement($likeLog)) {
            // set the owning side to null (unless already changed)
            if ($likeLog->getEntity() === $this) {
                $likeLog->setEntity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShareLog>
     */
    public function getShareLogs(): Collection
    {
        return $this->shareLogs;
    }

    public function addShareLog(ShareLog $shareLog): self
    {
        if (!$this->shareLogs->contains($shareLog)) {
            $this->shareLogs[] = $shareLog;
            $shareLog->setEntity($this);
        }

        return $this;
    }

    public function removeShareLog(ShareLog $shareLog): self
    {
        if ($this->shareLogs->removeElement($shareLog)) {
            // set the owning side to null (unless already changed)
            if ($shareLog->getEntity() === $this) {
                $shareLog->setEntity(null);
            }
        }

        return $this;
    }

    /**
     * @return array[]
     */
    public function renderRealStats(): array
    {
        return [
            [
                'text' => '点赞 ' . $this->getLikeLogs()->count(),
                'fontStyle' => ['fontSize' => 12],
            ],
            [
                'text' => '收藏 ' . $this->getCollectLogs()->count(),
                'fontStyle' => ['fontSize' => 12],
            ],
            [
                'text' => '分享 ' . $this->getShareLogs()->count(),
                'fontStyle' => ['fontSize' => 12],
            ],
        ];
    }

    /**
     * 获取统计数据.
     */
    #[Groups(['restful_read'])]
    public function getRealStats(): array
    {
        return [
            'likeTotal' => $this->getLikeLogs()->count(),
            'collectTotal' => $this->getCollectLogs()->count(),
            'shareTotal' => $this->getShareLogs()->count(),
        ];
    }

    /**
     * 方便读取数据.
     */
    #[Groups(['restful_read'])]
    public function getValues(): array
    {
        $result = [];
        foreach ($this->getValueList() as $item) {
            $result[$item->getAttribute()->getName()] = $item->getCastData();
        }

        return $result;
    }

    public function getPublishTime(): ?\DateTimeInterface
    {
        return $this->publishTime;
    }

    public function setPublishTime(?\DateTimeInterface $publishTime): self
    {
        $this->publishTime = $publishTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setEntity($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEntity() === $this) {
                $comment->setEntity(null);
            }
        }

        return $this;
    }}
