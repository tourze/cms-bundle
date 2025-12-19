<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\CmsBundle\Entity\Attribute;
use Tourze\CmsBundle\Entity\Model;
use Tourze\CmsBundle\Entity\Value;

#[When(env: 'test')]
#[When(env: 'dev')]
final class ValueFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $model = $this->getReference(ModelFixtures::MODEL_ENTERTAINMENT_REFERENCE, Model::class);
        $attribute = $this->getReference(AttributeFixtures::ATTRIBUTE_CONTENT_REFERENCE, Attribute::class);

        $contentData = [
            '<p>篮球（basketball），是以手为中心的身体对抗性体育运动，是奥运会核心比赛项目。</p>',
            '<p>足球（Football），是一项以脚为主，控制和支配球，两支球队按照一定规则在同一块长方形球场上互相进行进攻、防守对抗的体育运动项目。</p>',
        ];

        foreach ($contentData as $content) {
            $value = new Value();
            $value->setModel($model);
            $value->setAttribute($attribute);
            $value->setData($content);
            $value->setRawData([
                'v' => $content,
            ]);
            $manager->persist($value);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ModelFixtures::class,
            AttributeFixtures::class,
        ];
    }
}
