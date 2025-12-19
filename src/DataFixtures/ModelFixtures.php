<?php

declare(strict_types=1);

namespace Tourze\CmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\CmsBundle\Entity\Model;

#[When(env: 'test')]
#[When(env: 'dev')]
final class ModelFixtures extends Fixture
{
    public const MODEL_ENTERTAINMENT_REFERENCE = 'model-entertainment';

    public function load(ObjectManager $manager): void
    {
        $model = new Model();
        $model->setValid(true);
        $model->setTitle('娱乐文章');
        $model->setCode('娱乐文章');
        $model->setSortNumber(0);
        $model->setAllowLike(true);
        $model->setAllowCollect(true);
        $model->setAllowShare(true);

        $manager->persist($model);
        $this->addReference(self::MODEL_ENTERTAINMENT_REFERENCE, $model);

        $manager->flush();
    }
}
