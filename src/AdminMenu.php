<?php

namespace CmsBundle;

use CmsBundle\Entity\Category;
use CmsBundle\Entity\CollectLog;
use CmsBundle\Entity\Entity;
use CmsBundle\Entity\LikeLog;
use CmsBundle\Entity\Model;
use CmsBundle\Entity\RenderTemplate;
use CmsBundle\Entity\Tag;
use CmsBundle\Entity\Topic;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

#[MenuProvider]
class AdminMenu
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('内容中心')) {
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

        $item->getChild('内容中心')->addChild('内容管理')->setUri($this->linkGenerator->getCurdListPage(Entity::class));
        $item->getChild('内容中心')->addChild('模型管理')->setUri($this->linkGenerator->getCurdListPage(Model::class));
        $item->getChild('内容中心')->addChild('目录管理')->setUri($this->linkGenerator->getCurdListPage(Category::class));
        $item->getChild('内容中心')->addChild('专题管理')->setUri($this->linkGenerator->getCurdListPage(Topic::class));
        $item->getChild('内容中心')->addChild('标签管理')->setUri($this->linkGenerator->getCurdListPage(Tag::class));
        $item->getChild('内容中心')->addChild('渲染模板')->setUri($this->linkGenerator->getCurdListPage(RenderTemplate::class));
        $item->getChild('内容中心')->addChild('收藏日志')->setUri($this->linkGenerator->getCurdListPage(CollectLog::class));
        $item->getChild('内容中心')->addChild('点赞日志')->setUri($this->linkGenerator->getCurdListPage(LikeLog::class));
    }
}
