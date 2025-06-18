<?php

namespace CmsBundle\Entity;

use CmsBundle\Enum\FieldType;
use CmsBundle\Repository\ValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Yiisoft\Arrays\ArrayHelper;

#[ORM\Entity(repositoryClass: ValueRepository::class)]
#[ORM\Table(name: 'cms_value', options: ['comment' => 'cms数据表'])]
#[ORM\UniqueConstraint(name: 'cms_value_idx_uniq', columns: ['model_id', 'attribute_id', 'entity_id'])]
class Value
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Model $model = null;

    #[Groups(['restful_read'])]
    #[ORM\ManyToOne(targetEntity: Attribute::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Attribute $attribute = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Entity::class, inversedBy: 'valueList', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Entity $entity = null;

    #[ORM\Column(type: Types::JSON, options: ['comment' => '原始数据'])]
    private array $rawData = [];

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $data = null;

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

    public function getId(): ?string
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

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    public function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCastData(): mixed
    {
        if (FieldType::INTEGER === $this->getAttribute()->getType()) {
            return intval($this->getData());
        }

        if (FieldType::SINGLE_IMAGE === $this->getAttribute()->getType()) {
            $data = $this->getData();
            $data = json_decode($data, true);

            return isset($data[0]) ? $data[0]['url'] : null;
        }

        if (FieldType::MULTIPLE_IMAGE === $this->getAttribute()->getType()) {
            $data = $this->getData();
            if (!$data) {
                return [];
            }

            $data = json_decode($data, true);
            $res = [];
            if (!is_array($data)) {
                return $res;
            }

            foreach ($data as $datum) {
                $res[] = ArrayHelper::getValue($datum, 'url');
            }

            return $res;
        }

        return $this->getData();
    }}
