<?php

namespace App\Listener;

use App\Entity\Image;
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
        $image = $args->getEntity();
        // @todo update
        if($image instanceof Image) {
            $this->cacheManager->remove($this->uploaderHelper->asset($image, 'imageFile'));
        }
    }

    public function preUpdate(PreUpdateEventArgs $args) {
        $image = $args->getEntity();
        if($image instanceof Image &&
            ($image->getImageFile() instanceof UploadedFile || $image->getImageFile() === null)
        ) {
            $this->cacheManager->remove($this->uploaderHelper->asset($image, 'imageFile'));
        }
    }
}