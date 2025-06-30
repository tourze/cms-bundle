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
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Yiisoft\Arrays\ArrayHelper;

#[ORM\Entity(repositoryClass: ValueRepository::class)]
#[ORM\Table(name: 'cms_value', options: ['comment' => 'cms数据表'])]
#[ORM\UniqueConstraint(name: 'cms_value_idx_uniq', columns: ['model_id', 'attribute_id', 'entity_id'])]
class Value implements \Stringable
{
    use TimestampableAware;
    use \Tourze\DoctrineUserBundle\Traits\BlameableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Model $model = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\ManyToOne(targetEntity: Attribute::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Attribute $attribute = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Entity::class, inversedBy: 'valueList', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Entity $entity = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '原始数据'])]
    private array $rawData = [];

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '数据内容'])]
    private ?string $data = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        if ($this->getId() === null) {
            return '';
        }

        return (string) $this->getId();
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

    /**
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * @param array<string, mixed> $rawData
     */
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
        $attribute = $this->getAttribute();
        if ($attribute === null) {
            return $this->getData();
        }

        if (FieldType::INTEGER === $attribute->getType()) {
            return intval($this->getData());
        }

        if (FieldType::SINGLE_IMAGE === $attribute->getType()) {
            $data = $this->getData();
            if ($data === null) {
                return null;
            }
            $decoded = json_decode($data, true);
            if (!is_array($decoded)) {
                return null;
            }

            return isset($decoded[0]['url']) ? $decoded[0]['url'] : null;
        }

        if (FieldType::MULTIPLE_IMAGE === $attribute->getType()) {
            $data = $this->getData();
            if ($data === null || $data === '') {
                return [];
            }

            $decoded = json_decode($data, true);
            $res = [];
            if (!is_array($decoded)) {
                return $res;
            }

            foreach ($decoded as $datum) {
                if (is_array($datum)) {
                    $res[] = ArrayHelper::getValue($datum, 'url');
                }
            }

            return $res;
        }

        return $this->getData();
    }}
