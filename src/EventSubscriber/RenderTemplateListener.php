<?php

namespace CmsBundle\EventSubscriber;

use CmsBundle\Entity\RenderTemplate;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: RenderTemplate::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: RenderTemplate::class)]
class RenderTemplateListener
{
    public function prePersist(RenderTemplate $object): void
    {
        $this->ensurePathHasLeftSlash($object);
    }

    public function preUpdate(RenderTemplate $object): void
    {
        $this->ensurePathHasLeftSlash($object);
    }

    public function ensurePathHasLeftSlash(RenderTemplate $object): void
    {
        $path = trim($object->getPath());
        if (!str_starts_with($path, '/')) {
            $path = "/{$path}";
        }

        $object->setPath($path);
    }
}
