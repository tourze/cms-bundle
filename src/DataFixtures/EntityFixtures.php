<?php

declare(strict_types=1);

namespace CmsBundle\DataFixtures;

use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Enum\EntityState;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class EntityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $model = $this->getReference(ModelFixtures::MODEL_ENTERTAINMENT_REFERENCE, Model::class);

        $articlesData = [
            ['篮球的魅力', '篮球的魅力'],
            ['足球的魅力', '足球的魅力'],
        ];

        foreach ($articlesData as [$title, $remark]) {
            $article = new Entity();
            $article->setTitle($title);
            $article->setRemark($remark);
            $article->setModel($model);
            $article->setState(EntityState::PUBLISHED);
            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ModelFixtures::class,
        ];
    }
}
