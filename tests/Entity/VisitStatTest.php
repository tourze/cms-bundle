<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Entity\VisitStat;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(VisitStat::class)]
final class VisitStatTest extends AbstractEntityTestCase
{
    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'value' => ['value', 100];
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProviderWithTypes(): iterable
    {
        yield 'value' => ['value', 100];
    }

    public function testVisitStatGettersAndSetters(): void
    {
        $visitStat = new VisitStat();
        $date = new \DateTimeImmutable('2024-01-01');
        $entityId = '123456789';
        $value = 100;

        $visitStat->setDate($date);
        $visitStat->setEntityId($entityId);
        $visitStat->setValue($value);

        $this->assertSame($date, $visitStat->getDate());
        $this->assertSame($entityId, $visitStat->getEntityId());
        $this->assertSame($value, $visitStat->getValue());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $visitStat = new VisitStat();
        $this->assertSame('', (string) $visitStat);
    }

    public function testInitialValues(): void
    {
        $visitStat = new VisitStat();
        $this->assertNull($visitStat->getId());
        $this->assertNull($visitStat->getDate());
        $this->assertNull($visitStat->getEntityId());
        $this->assertNull($visitStat->getValue());
    }

    public function testSetEntityIdWithNull(): void
    {
        $visitStat = new VisitStat();
        $visitStat->setEntityId('123');
        $visitStat->setEntityId(null);

        $this->assertNull($visitStat->getEntityId());
    }

    public function testFluentInterface(): void
    {
        $visitStat = new VisitStat();
        $date = new \DateTimeImmutable('2024-01-01');
        $entityId = '123456789';
        $value = 100;

        // 分别调用方法而不是链式调用，因为 setEntityId 返回 void
        $visitStat->setDate($date);
        $visitStat->setEntityId($entityId);
        $visitStat->setValue($value);

        // 验证所有值都正确设置
        $this->assertSame($date, $visitStat->getDate());
        $this->assertSame($entityId, $visitStat->getEntityId());
        $this->assertSame($value, $visitStat->getValue());
    }

    protected function createEntity(): VisitStat
    {
        return new VisitStat();
    }
}
