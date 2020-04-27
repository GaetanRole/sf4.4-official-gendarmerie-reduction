<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ImageHandler;
use Doctrine\ORM\Event\OnFlushEventArgs;
use \Exception;
use App\Entity\Image;
use App\Entity\Reduction;
use App\Service\GlobalClock;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ImageUploadListener
{
    /** @var ImageHandler */
    private $imageHandler;

    /** @var GlobalClock */
    private $clock;

    public function __construct(ImageHandler $imageHandler, GlobalClock $globalClock)
    {
        $this->imageHandler = $imageHandler;
        $this->clock = $globalClock;
    }

    /**
     * On a new and edit, upload the new file and recompute Reduction entity.
     *
     * @throws Exception From DateTime provided by GlobalClock and Uuid::uuid4
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates());

        foreach ($entities as $entity) {
            if ($entity instanceof Reduction && $entity->getImage()) {
                $this->uploadFile($this->prepareImageEntity($entity->getImage()));

                $classMetadata = $em->getClassMetadata(Image::class);
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity->getImage());
            }
        }
    }

    /**
     * Find and set the file to the passed Image entity during a select.
     */
    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Reduction) {
            return;
        }

        $image = $entity->getImage();
        if ($image) {
            $file = $image->getFilePath($this->imageHandler->getTargetDirectory());
            if (file_exists($file)) {
                $image->setFile(new File($file));
            }
        }
    }

    /**
     * Prepare the Image entity to receive a new file.
     *
     * @throws Exception From DateTime provided by GlobalClock and Uuid::uuid4
     */
    private function prepareImageEntity(Image $image): Image
    {
        if ($image->getCreatedAt()) {
            $image->setUpdatedAt($this->clock->getNowInDateTime());
            return $image;
        }

        $image->setUuid(Uuid::uuid4());
        $image->setCreatedAt($this->clock->getNowInDateTime());

        return $image;
    }

    /**
     * Set the uploaded file to the Image entity.
     */
    private function uploadFile(Image $image): void
    {
        $file = $image->getFile();
        $file instanceof UploadedFile ?
            $image->setFile($this->imageHandler->upload($file, $image)) : $image->setFile($file);
    }
}
