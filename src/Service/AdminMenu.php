<?php

declare(strict_types=1);

namespace CmsBundle\Service;

use CmsBundle\Entity\Attribute;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\Model;
use CmsBundle\Entity\Value;
use CmsBundle\Entity\VisitStat;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\TagManageBundle\Entity\Tag;

readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('内容中心')) {
            $item->addChild('内容中心')->setExtra('permission', 'CmsBundle');
        }

        //        $entityClass = Entity::class;
        //        foreach ($this->modelRepository->findBy(['active' => true]) as $model) {
        //            if (!empty($model->getAttributes())) {
        //                $item->getChild('CMS')->addChild("{$model->getTitle()}管理", [
        //                    'uri' => "/diy-list/AdminGetCurdListPage?_model_class={$entityClass}&model_id={$model->getId()}",
        //                ]);
        //            }
        //        }

        $contentCenter = $item->getChild('内容中心');
        if (null !== $contentCenter) {
            $contentCenter->addChild('内容管理')->setUri($this->linkGenerator->getCurdListPage(Entity::class));
            $contentCenter->addChild('模型管理')->setUri($this->linkGenerator->getCurdListPage(Model::class));
            $contentCenter->addChild('属性管理')->setUri($this->linkGenerator->getCurdListPage(Attribute::class));
            $contentCenter->addChild('数据值管理')->setUri($this->linkGenerator->getCurdListPage(Value::class));
            $contentCenter->addChild('访问统计')->setUri($this->linkGenerator->getCurdListPage(VisitStat::class));
            $contentCenter->addChild('标签管理')->setUri($this->linkGenerator->getCurdListPage(Tag::class));
        }
    }
}
