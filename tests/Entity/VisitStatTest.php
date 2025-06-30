<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\VisitStat;
use PHPUnit\Framework\TestCase;

class VisitStatTest extends TestCase
{
    private VisitStat $visitStat;

    protected function setUp(): void
    {
        $this->visitStat = new VisitStat();
    }

    public function testGettersAndSetters(): void
    {
        $date = new \DateTimeImmutable('2024-01-01');
        $entityId = '123456789';
        $value = 100;

        $this->visitStat->setDate($date);
        $this->visitStat->setEntityId($entityId);
        $this->visitStat->setValue($value);

        $this->assertSame($date, $this->visitStat->getDate());
        $this->assertSame($entityId, $this->visitStat->getEntityId());
        $this->assertSame($value, $this->visitStat->getValue());
    }

    public function testStringable(): void
    {
        // 测试空ID时返回空字符串
        $this->assertSame('', (string) $this->visitStat);
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->visitStat->getId());
        $this->assertNull($this->visitStat->getDate());
        $this->assertNull($this->visitStat->getEntityId());
        $this->assertNull($this->visitStat->getValue());
    }

    public function testSetEntityIdWithNull(): void
    {
        $this->visitStat->setEntityId('123');
        $this->visitStat->setEntityId(null);

        $this->assertNull($this->visitStat->getEntityId());
    }

    public function testFluentInterface(): void
    {
        $date = new \DateTimeImmutable('2024-01-01');
        $entityId = '123456789';
        $value = 100;

        $result = $this->visitStat
            ->setDate($date)
            ->setEntityId($entityId)
            ->setValue($value);

        $this->assertSame($this->visitStat, $result);
    }
}
