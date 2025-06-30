<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\FieldType;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    private Attribute $attribute;

    protected function setUp(): void
    {
        $this->attribute = new Attribute();
    }

    public function testGettersAndSetters(): void
    {
        $name = 'Test Attribute';
        $type = FieldType::STRING;
        $model = $this->createMock(Model::class);
        $required = true;
        $displayOrder = 100;
        $searchable = true;

        $this->attribute->setName($name);
        $this->attribute->setType($type);
        $this->attribute->setModel($model);
        $this->attribute->setRequired($required);
        $this->attribute->setDisplayOrder($displayOrder);
        $this->attribute->setSearchable($searchable);

        $this->assertSame($name, $this->attribute->getName());
        $this->assertSame($type, $this->attribute->getType());
        $this->assertSame($model, $this->attribute->getModel());
        $this->assertSame($required, $this->attribute->getRequired());
        $this->assertSame($displayOrder, $this->attribute->getDisplayOrder());
        $this->assertSame($searchable, $this->attribute->getSearchable());
    }

    public function testStringable(): void
    {
        $this->assertSame('', (string) $this->attribute);

        $this->attribute->setName('Test Attribute');
        $this->assertSame('Test Attribute', (string) $this->attribute);
    }

    public function testInitialValues(): void
    {
        $this->assertSame(0, $this->attribute->getId());
        $this->assertNull($this->attribute->getName());

        // Debug output
        $type = $this->attribute->getType();
        if ($type !== null) {
            echo "Type is not null, actual value: " . var_export($type, true) . "\n";
            echo "Type class: " . get_class($type) . "\n";
        }

        $this->assertNull($this->attribute->getType());
        $this->assertNull($this->attribute->getModel());
        $this->assertNull($this->attribute->getRequired());
        $this->assertSame(0, $this->attribute->getDisplayOrder());
        $this->assertFalse($this->attribute->getSearchable());
    }

    public function testFluentInterface(): void
    {
        $name = 'Test Attribute';
        $type = FieldType::STRING;
        $model = $this->createMock(Model::class);

        $result = $this->attribute
            ->setName($name)
            ->setType($type)
            ->setModel($model);

        $this->assertSame($this->attribute, $result);
    }
}
