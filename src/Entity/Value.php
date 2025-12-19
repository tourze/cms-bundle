<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\CmsBundle\Enum\FieldType;
use Tourze\CmsBundle\Repository\ValueRepository;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\DoctrineUserBundle\Traits\CreateUserAware;
use Yiisoft\Arrays\ArrayHelper;

#[ORM\Entity(repositoryClass: ValueRepository::class)]
#[ORM\Table(name: 'cms_value', options: ['comment' => 'cms数据表'])]
#[ORM\UniqueConstraint(name: 'cms_value_idx_uniq', columns: ['model_id', 'attribute_id', 'entity_id'])]
class Value implements \Stringable
{
    use BlameableAware;
    use CreateUserAware;
    use IpTraceableAware;
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', onDelete: 'SET NULL')]
    private ?Model $model = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\ManyToOne(targetEntity: Attribute::class)]
    #[ORM\JoinColumn(name: 'attribute_id', onDelete: 'CASCADE')]
    private ?Attribute $attribute = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Entity::class, cascade: ['persist', 'remove'], inversedBy: 'valueList')]
    #[ORM\JoinColumn(name: 'entity_id', onDelete: 'CASCADE')]
    private ?Entity $entity = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '原始数据'])]
    #[Assert\Type(type: 'array')]
    private array $rawData = [];

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '数据内容'])]
    #[Assert\Length(max: 65535)]
    private ?string $data = null;

    public function __toString(): string
    {
        return $this->getData() ?? '';
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): void
    {
        $this->model = $model;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): void
    {
        $this->attribute = $attribute;
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
    public function setRawData(array $rawData): void
    {
        $this->rawData = $rawData;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): void
    {
        $this->entity = $entity;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): void
    {
        $this->data = $data;
    }

    public function getCastData(): mixed
    {
        $attribute = $this->getAttribute();
        if (null === $attribute) {
            return $this->getData();
        }

        return match ($attribute->getType()) {
            FieldType::INTEGER => $this->castToInteger(),
            FieldType::SINGLE_IMAGE => $this->castToSingleImage(),
            FieldType::MULTIPLE_IMAGE => $this->castToMultipleImages(),
            default => $this->getData(),
        };
    }

    private function castToInteger(): int
    {
        return (int) $this->getData();
    }

    private function castToSingleImage(): ?string
    {
        $data = $this->getData();
        if (null === $data) {
            return null;
        }

        $decoded = json_decode($data, true);
        if (!\is_array($decoded)) {
            return null;
        }

        $firstItem = $decoded[0] ?? null;
        if (!\is_array($firstItem)) {
            return null;
        }

        $url = $firstItem['url'] ?? null;

        return \is_string($url) ? $url : null;
    }

    /**
     * @return array<string>
     */
    private function castToMultipleImages(): array
    {
        $data = $this->getData();
        if (null === $data || '' === $data) {
            return [];
        }

        $decoded = json_decode($data, true);
        if (!\is_array($decoded)) {
            return [];
        }

        $urls = [];
        foreach ($decoded as $datum) {
            if (\is_array($datum)) {
                $url = ArrayHelper::getValue($datum, 'url');
                if (\is_string($url)) {
                    $urls[] = $url;
                }
            }
        }

        return $urls;
    }
}
