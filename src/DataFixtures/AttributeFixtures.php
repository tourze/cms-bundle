<?php

declare(strict_types=1);

namespace CmsBundle\DataFixtures;

use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\FieldType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AttributeFixtures extends Fixture implements DependentFixtureInterface
{
    public const ATTRIBUTE_CONTENT_REFERENCE = 'attribute-content';

    public function load(ObjectManager $manager): void
    {
        $model = $this->getReference(ModelFixtures::MODEL_ENTERTAINMENT_REFERENCE, Model::class);

        $attribute = new Attribute();
        $attribute->setModel($model);
        $attribute->setType(FieldType::RICH_TEXT);
        $attribute->setTitle('内容');
        $attribute->setValid(true);
        $attribute->setSearchable(true);
        $attribute->setDisplayOrder(2);
        $attribute->setName('content');
        $attribute->setSpan(24);
        $attribute->setRequired(true);

        $manager->persist($attribute);
        $this->addReference(self::ATTRIBUTE_CONTENT_REFERENCE, $attribute);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ModelFixtures::class,
        ];
    }
}
