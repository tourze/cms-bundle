<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsBundle\Entity\Attribute;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Enum\FieldType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Attribute::class)]
final class AttributeTest extends AbstractEntityTestCase
{
    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'name' => ['name', 'test-attribute'];
        yield 'title' => ['title', 'Test Attribute'];
        yield 'type' => ['type', FieldType::STRING];
        yield 'defaultValue' => ['defaultValue', 'default-value'];
        yield 'required' => ['required', true];
        yield 'length' => ['length', 255];
        yield 'span' => ['span', 12];
        yield 'searchable' => ['searchable', true];
        yield 'displayOrder' => ['displayOrder', 10];
        yield 'config' => ['config', 'test-config'];
        yield 'importable' => ['importable', true];
        yield 'placeholder' => ['placeholder', 'test placeholder'];
        yield 'valid' => ['valid', true];
    }

    public function testConstruct(): void
    {
        $attribute = new Attribute();
        $this->assertInstanceOf(Attribute::class, $attribute);
    }

    public function testSettersAndGetters(): void
    {
        $attribute = new Attribute();

        $attribute->setName('test-name');
        $this->assertSame('test-name', $attribute->getName());

        $attribute->setTitle('Test Title');
        $this->assertSame('Test Title', $attribute->getTitle());

        $attribute->setType(FieldType::TEXT);
        $this->assertSame(FieldType::TEXT, $attribute->getType());

        $attribute->setDefaultValue('default-value');
        $this->assertSame('default-value', $attribute->getDefaultValue());

        $attribute->setRequired(true);
        $this->assertTrue($attribute->getRequired());

        $attribute->setLength(255);
        $this->assertSame(255, $attribute->getLength());

        $attribute->setSpan(12);
        $this->assertSame(12, $attribute->getSpan());

        $attribute->setSearchable(true);
        $this->assertTrue($attribute->getSearchable());

        $attribute->setDisplayOrder(10);
        $this->assertSame(10, $attribute->getDisplayOrder());

        $attribute->setConfig('test-config');
        $this->assertSame('test-config', $attribute->getConfig());

        $attribute->setImportable(true);
        $this->assertTrue($attribute->isImportable());

        $attribute->setPlaceholder('test placeholder');
        $this->assertSame('test placeholder', $attribute->getPlaceholder());

        $attribute->setValid(true);
        $this->assertTrue($attribute->isValid());
    }

    public function testModelRelation(): void
    {
        $attribute = new Attribute();
        $model = new Model();

        $attribute->setModel($model);
        $this->assertSame($model, $attribute->getModel());
    }

    public function testToString(): void
    {
        $attribute = new Attribute();
        $attribute->setName('test-name');
        $attribute->setTitle('Test Title');

        $this->assertSame('Test Title(test-name)', (string) $attribute);

        $attributeWithoutTitle = new Attribute();
        $attributeWithoutTitle->setName('test-name');
        $this->assertSame('test-name', (string) $attributeWithoutTitle);

        $emptyAttribute = new Attribute();
        $this->assertSame('', (string) $emptyAttribute);
    }

    protected function createEntity(): Attribute
    {
        return new Attribute();
    }
}
