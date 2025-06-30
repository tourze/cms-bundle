<?php

namespace CmsBundle\Tests\Entity;

use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Entity\Value;
use CmsBundle\Enum\FieldType;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    private Value $value;

    protected function setUp(): void
    {
        $this->value = new Value();
    }

    public function testGettersAndSetters(): void
    {
        $model = $this->createMock(Model::class);
        $attribute = $this->createMock(Attribute::class);
        $entity = $this->createMock(Entity::class);
        $rawData = ['key' => 'value'];
        $data = 'test data';
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->value->setModel($model);
        $this->value->setAttribute($attribute);
        $this->value->setEntity($entity);
        $this->value->setRawData($rawData);
        $this->value->setData($data);
        $this->value->setCreatedFromIp($createdFromIp);
        $this->value->setUpdatedFromIp($updatedFromIp);

        $this->assertSame($model, $this->value->getModel());
        $this->assertSame($attribute, $this->value->getAttribute());
        $this->assertSame($entity, $this->value->getEntity());
        $this->assertSame($rawData, $this->value->getRawData());
        $this->assertSame($data, $this->value->getData());
        $this->assertSame($createdFromIp, $this->value->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->value->getUpdatedFromIp());
    }

    public function testStringable(): void
    {
        $this->assertSame('', (string) $this->value);
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->value->getId());
        $this->assertNull($this->value->getModel());
        $this->assertNull($this->value->getAttribute());
        $this->assertNull($this->value->getEntity());
        $this->assertSame([], $this->value->getRawData());
        $this->assertNull($this->value->getData());
        $this->assertNull($this->value->getCreatedFromIp());
        $this->assertNull($this->value->getUpdatedFromIp());
    }

    public function testGetCastDataInteger(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::INTEGER);

        $this->value->setAttribute($attribute);
        $this->value->setData('123');

        $this->assertSame(123, $this->value->getCastData());
    }

    public function testGetCastDataSingleImage(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::SINGLE_IMAGE);

        $this->value->setAttribute($attribute);
        $this->value->setData('[{"url": "http://example.com/image.jpg"}]');

        $this->assertSame('http://example.com/image.jpg', $this->value->getCastData());
    }

    public function testGetCastDataSingleImageEmpty(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::SINGLE_IMAGE);

        $this->value->setAttribute($attribute);
        $this->value->setData('[]');

        $this->assertNull($this->value->getCastData());
    }

    public function testGetCastDataMultipleImage(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::MULTIPLE_IMAGE);

        $this->value->setAttribute($attribute);
        $this->value->setData('[{"url": "image1.jpg"}, {"url": "image2.jpg"}]');

        $expected = ['image1.jpg', 'image2.jpg'];
        $this->assertSame($expected, $this->value->getCastData());
    }

    public function testGetCastDataMultipleImageEmpty(): void
    {
        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getType')->willReturn(FieldType::MULTIPLE_IMAGE);

        $this->value->setAttribute($attribute);
        $this->value->setData('');

        $this->assertSame([], $this->value->getCastData());
    }

    public function testFluentInterface(): void
    {
        $model = $this->createMock(Model::class);
        $attribute = $this->createMock(Attribute::class);
        $entity = $this->createMock(Entity::class);
        $rawData = ['key' => 'value'];
        $data = 'test data';

        $result = $this->value
            ->setModel($model)
            ->setAttribute($attribute)
            ->setEntity($entity)
            ->setRawData($rawData)
            ->setData($data);

        $this->assertSame($this->value, $result);
    }
}
