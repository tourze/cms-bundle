<?php

declare(strict_types=1);

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Value;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Value::class)]
final class ValueTest extends AbstractEntityTestCase
{
    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'data' => ['data', 'test-value'];
    }

    public function testConstruct(): void
    {
        $value = new Value();
        $this->assertInstanceOf(Value::class, $value);
    }

    public function testValueGettersAndSetters(): void
    {
        $value = new Value();
        $data = 'test data';
        $value->setData($data);
        $this->assertSame($data, $value->getData());

        $rawData = ['key' => 'value'];
        $value->setRawData($rawData);
        $this->assertSame($rawData, $value->getRawData());

        $createdFromIp = '192.168.1.1';
        $value->setCreatedFromIp($createdFromIp);
        $this->assertSame($createdFromIp, $value->getCreatedFromIp());

        $updatedFromIp = '192.168.1.2';
        $value->setUpdatedFromIp($updatedFromIp);
        $this->assertSame($updatedFromIp, $value->getUpdatedFromIp());
    }

    public function testToString(): void
    {
        $value = new Value();
        $value->setData('test value');
        $this->assertSame('test value', (string) $value);
    }

    public function testToStringEmpty(): void
    {
        $value = new Value();
        $this->assertSame('', (string) $value);
    }

    protected function createEntity(): Value
    {
        return new Value();
    }
}
