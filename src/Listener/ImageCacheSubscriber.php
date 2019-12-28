<?php

namespace App\Listener;

use App\Entity\Trick;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args) {
        $trick = $args->getEntity();
        if($trick instanceof Trick) {
            $this->cacheManager->remove($this->uploaderHelper->asset($trick, 'imageFile'));
        }
    }

    public function preUpdate(PreUpdateEventArgs $args) {
        $trick = $args->getEntity();
        if($trick instanceof Trick &&
            ($trick->getImageFile() instanceof UploadedFile || $trick->getImageFile() === null)
        ) {
            $this->cacheManager->remove($this->uploaderHelper->asset($trick, 'imageFile'));
        }
    }
}