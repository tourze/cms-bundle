<?php

namespace CmsBundle\Entity;

use CmsBundle\Repository\VisitStatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: VisitStatRepository::class)]
#[ORM\Table(name: 'cms_visit_stat', options: ['comment' => '访问统计表'])]
#[ORM\UniqueConstraint(name: 'cms_stat_unique_entity_date', columns: ['entity_id', 'date'])]
class VisitStat implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    private ?\DateTimeImmutable $date = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => '文章ID'])]
    private ?string $entityId = null;

    #[ORM\Column(name: 'value', type: Types::INTEGER, nullable: false, options: ['comment' => '总数'])]
    private ?int $value = null;

    public function __toString(): string
    {
        if ($this->getId() === null) {
            return '';
        }

        return (string) $this->getId();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function setEntityId(?string $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }}
