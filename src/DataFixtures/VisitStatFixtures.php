<?php

declare(strict_types=1);

namespace CmsBundle\DataFixtures;

use CmsBundle\Entity\VisitStat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class VisitStatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $stats = [
            ['entityId' => '1', 'value' => 150],
            ['entityId' => '2', 'value' => 200],
            ['entityId' => '3', 'value' => 80],
        ];

        foreach ($stats as $statData) {
            $visitStat = new VisitStat();
            $visitStat->setEntityId($statData['entityId']);
            $visitStat->setValue($statData['value']);
            $visitStat->setDate(new \DateTimeImmutable());
            $manager->persist($visitStat);
        }

        $manager->flush();
    }
}
