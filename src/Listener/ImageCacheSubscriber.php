<?php

namespace App\Listener;

use App\Entity\Image;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    /** @var CacheManager */
    private $cacheManager;
    /** @var UploaderHelper */
    private $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'preRemove'
        ];
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof Image) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
        } elseif ($entity instanceof User) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'avatarFile'));
        }
    }
}