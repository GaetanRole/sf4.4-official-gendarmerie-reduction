<?php

declare(strict_types=1);

namespace App\EventListener;

use \Exception;
use App\Entity\Image;
use App\Entity\Reduction;
use App\Service\EntityManager\ImageManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * A Listener handling image upload.
 *
 * @todo Write unit tests for this listener.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ImageUploadListener
{
    /** @var ImageManager */
    private $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
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

        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if ($entity instanceof Reduction && $entity->getImage()) {
                $this->uploadFile($this->imageManager->prepareImageEntity($entity->getImage()));

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
            $file = $image->getFilePath($this->imageManager->getTargetDirectory());
            if (file_exists($file)) {
                $image->setFile(new File($file));
            }
        }
    }

    /**
     * Set the uploaded file to the Image entity.
     */
    private function uploadFile(Image $image): void
    {
        $file = $image->getFile();
        $file instanceof UploadedFile ?
            $image->setFile($this->imageManager->upload($file, $image)) : $image->setFile($file);
    }
}
