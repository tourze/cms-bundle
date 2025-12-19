<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\CmsBundle\Repository\VisitStatRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: VisitStatRepository::class)]
#[ORM\Table(name: 'cms_visit_stat', options: ['comment' => '访问统计表'])]
#[ORM\UniqueConstraint(name: 'cms_stat_unique_entity_date', columns: ['entity_id', 'date'])]
class VisitStat implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    #[Assert\NotNull]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $date = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => '文章ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $entityId = null;

    #[ORM\Column(name: 'value', type: Types::INTEGER, nullable: false, options: ['comment' => '总数'])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private ?int $value = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return $this->getId();
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function setEntityId(?string $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }
}
