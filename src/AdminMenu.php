<?php

namespace CmsBundle;

use CmsBundle\Entity\Category;
use CmsBundle\Entity\CollectLog;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\LikeLog;
use CmsBundle\Entity\Model;
use CmsBundle\Entity\Tag;
use CmsBundle\Entity\Topic;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('内容中心') === null) {
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
        if ($contentCenter !== null) {
            $contentCenter->addChild('内容管理')->setUri($this->linkGenerator->getCurdListPage(Entity::class));
            $contentCenter->addChild('模型管理')->setUri($this->linkGenerator->getCurdListPage(Model::class));
            $contentCenter->addChild('目录管理')->setUri($this->linkGenerator->getCurdListPage(Category::class));
            $contentCenter->addChild('专题管理')->setUri($this->linkGenerator->getCurdListPage(Topic::class));
            $contentCenter->addChild('标签管理')->setUri($this->linkGenerator->getCurdListPage(Tag::class));
            $contentCenter->addChild('收藏日志')->setUri($this->linkGenerator->getCurdListPage(CollectLog::class));
            $contentCenter->addChild('点赞日志')->setUri($this->linkGenerator->getCurdListPage(LikeLog::class));
        }
    }
}
