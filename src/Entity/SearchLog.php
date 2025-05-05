<?php

namespace CmsBundle\Entity;

use CmsBundle\Repository\SearchLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DoctrineEnhanceBundle\Traits\TimestampableAware;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

/**
 * 搜索记录表
 */
#[ORM\Table(name: 'ims_cms_search', options: ['comment' => '搜索记录表'])]
#[ORM\Entity(repositoryClass: SearchLogRepository::class)]
class SearchLog
{
    use TimestampableAware;

    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '用户ID'])]
    private int $memberId;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: false, options: ['default' => '', 'comment' => '关键词'])]
    private string $keyword;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '搜索目录ID'])]
    private int $categoryId = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '搜索专题ID'])]
    private int $topicId = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 1, 'comment' => '搜索次数'])]
    private int $count = 1;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '叠加命中数'])]
    private int $hit = 0;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'keyword' => $this->getKeyword(),
            'count' => $this->getCount(),
        ];
    }

    public function getMemberId(): int
    {
        return $this->memberId;
    }

    public function setMemberId(int $memberId): void
    {
        $this->memberId = $memberId;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getTopicId(): int
    {
        return $this->topicId;
    }

    public function setTopicId(int $topicId): void
    {
        $this->topicId = $topicId;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getHit(): int
    {
        return $this->hit;
    }

    public function setHit(int $hit): void
    {
        $this->hit = $hit;
    }
}
