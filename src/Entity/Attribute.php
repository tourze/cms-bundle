<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\CmsBundle\Enum\FieldType;
use Tourze\CmsBundle\Repository\AttributeRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Selectable;

#[ORM\Table(name: 'cms_attribute', options: ['comment' => 'cms属性表'])]
#[ORM\Entity(repositoryClass: AttributeRepository::class)]
class Attribute implements \Stringable
{
    use BlameableAware;
    use IpTraceableAware;
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    public ?int $id = null;

    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Model $model = null;

    #[TrackColumn]
    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '英文名'])]
    private ?string $name = null;

    #[TrackColumn]
    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '中文名'])]
    private ?string $title = null;

    #[TrackColumn]
    #[Groups(groups: ['restful_read'])]
    #[Assert\Choice(callback: [FieldType::class, 'cases'])]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, enumType: FieldType::class, options: ['comment' => '数据类型'])]
    private ?FieldType $type = null;

    #[TrackColumn]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '默认值'])]
    private ?string $defaultValue = null;

    #[TrackColumn]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否必填'])]
    private ?bool $required = null;

    #[TrackColumn]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '数据长度'])]
    private ?int $length = null;

    #[TrackColumn]
    #[Assert\Positive]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 24, 'comment' => '编辑宽度'])]
    private ?int $span = null;

    #[TrackColumn]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否可搜索'])]
    private bool $searchable = false;

    #[TrackColumn]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '支持导入'])]
    private ?bool $importable = false;

    #[TrackColumn]
    #[Assert\GreaterThanOrEqual(value: 0)]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '排序'])]
    private int $displayOrder = 0;

    #[TrackColumn]
    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '配置'])]
    private ?string $config = null;

    #[Assert\Length(max: 200)]
    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '占位提示文本'])]
    private ?string $placeholder = null;

    public function __toString(): string
    {
        $name = $this->getName();
        if (null === $name) {
            return '';
        }

        $title = $this->getTitle();
        if (null !== $title) {
            return "{$title}({$name})";
        }

        return $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?FieldType
    {
        return $this->type;
    }

    public function setType(?FieldType $type): void
    {
        $this->type = $type;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): void
    {
        $this->length = $length;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(?string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): void
    {
        $this->model = $model;
    }

    public function getSpan(): ?int
    {
        return $this->span;
    }

    public function setSpan(int $span): void
    {
        $this->span = $span;
    }

    public function getSearchable(): ?bool
    {
        return $this->searchable;
    }

    public function setSearchable(bool $searchable): void
    {
        $this->searchable = $searchable;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): void
    {
        $this->displayOrder = $displayOrder;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function genSelectOptions(EntityManagerInterface $entityManager): array
    {
        $type = $this->getType();
        if (null === $type || !$this->isSelectableType($type)) {
            return [];
        }

        $config = $this->getConfig();
        if (null === $config) {
            return [];
        }

        return $this->processSelectOptions($entityManager, $config);
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function setConfig(?string $config): void
    {
        $this->config = $config;
    }

    public function isImportable(): ?bool
    {
        return $this->importable;
    }

    public function setImportable(?bool $importable): void
    {
        $this->importable = $importable;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(?string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    private function isSelectableType(FieldType $type): bool
    {
        return \in_array($type, [FieldType::SINGLE_SELECT, FieldType::MULTIPLE_SELECT, FieldType::TAGS_SELECT], true);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function processSelectOptions(EntityManagerInterface $entityManager, string $config): array
    {
        if (class_exists($config) && is_subclass_of($config, Selectable::class)) {
            return $this->getEnumOptions($config);
        }

        if (str_starts_with($config, 'Entity') || str_starts_with($config, 'SELECT')) {
            return $this->getDatabaseOptions($entityManager, $config);
        }

        return $this->getTextOptions($config);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getEnumOptions(string $className): array
    {
        /** @var array<int, array<string, mixed>> $options */
        $options = $className::genOptions();

        return $options;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getDatabaseOptions(EntityManagerInterface $entityManager, string $config): array
    {
        $sql = $this->buildSqlQuery($config);
        $options = $entityManager->getConnection()->executeQuery($sql)->fetchAllAssociative();

        return $this->normalizeDatabaseOptions($options);
    }

    private function buildSqlQuery(string $config): string
    {
        if (str_starts_with($config, 'Entity')) {
            $arr = explode(':', $config);

            return "select distinct {$arr[2]} as label,{$arr[3]} as id from {$arr[1]} order by id desc ";
        }

        return $config;
    }

    /**
     * @param array<int, array<string, mixed>> $options
     *
     * @return array<int, array<string, mixed>>
     */
    private function normalizeDatabaseOptions(array $options): array
    {
        foreach ($options as $k => $v) {
            if (isset($v['label'])) {
                $v['name'] = $v['label'];
                $v['text'] = $v['label'];
            }

            if (isset($v['id'])) {
                $v['value'] = $v['id'];
            }

            $options[$k] = $v;
        }

        return $this->normalizeOptionValues($options);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getTextOptions(string $config): array
    {
        $lines = explode("\n", $config);
        $options = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ('' === $line) {
                continue;
            }

            $options[] = [
                'label' => $line,
                'text' => $line,
                'value' => $line,
                'name' => $line,
            ];
        }

        return $this->normalizeOptionValues($options);
    }

    /**
     * @param array<int, array<string, mixed>> $options
     *
     * @return array<int, array<string, mixed>>
     */
    private function normalizeOptionValues(array $options): array
    {
        foreach ($options as $k => $v) {
            if (isset($v['value'])) {
                $value = $v['value'];
                if (\is_scalar($value) || (\is_object($value) && method_exists($value, '__toString'))) {
                    $v['value'] = (string) $value;
                } else {
                    $v['value'] = '';
                }
            }
            $options[$k] = $v;
        }

        return $options;
    }
}
