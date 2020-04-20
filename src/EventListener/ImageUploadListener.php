<?php

declare(strict_types = 1);

namespace App\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use \Exception;
use App\Entity\Image;
use App\Entity\Reduction;
use App\Services\GlobalClock;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Services\Uploader\ImageUploader;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ImageUploadListener
{
    /** @var ImageUploader */
    private $uploader;

    /** @var GlobalClock */
    private $clock;

    public function __construct(ImageUploader $uploader, GlobalClock $globalClock)
    {
        $this->uploader = $uploader;
        $this->clock = $globalClock;
    }

    /**
     * @throws Exception From DateTime provided by GlobalClock and Uuid::uuid4
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Image && (!$entity instanceof Reduction || !$entity->getImage())) {
            return;
        }

        $this->uploadFile(
            $this->prepareImageEntity($entity instanceof Image ? $entity : $entity->getImage())
        );
    }

    /**
     * @throws Exception From DateTime provided by GlobalClock and Uuid::uuid4
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = $uow->getScheduledEntityUpdates();

        if ($entities) {
            /** @var Reduction $entity */
            $entity = $entities[array_key_first($entities)];

            if ($entity instanceof Reduction && $entity->getImage()) {
                $this->uploadFile($this->prepareImageEntity($entity->getImage()));

                $classMetadata = $em->getClassMetadata(Image::class);
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity->getImage());
            }
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Reduction) {
            return;
        }

        $image = $entity->getImage();
        if ($image) {
            $file = $image->getFilePath($this->uploader->getTargetDirectory());
            if (file_exists($file)) {
                $image->setFile(new File($file));
            }
        }
    }

    /**
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

    private function uploadFile(Image $image): void
    {
        $file = $image->getFile();
        $file instanceof UploadedFile ?
            $image->setFile($this->uploader->upload($file, $image)) : $image->setFile($file);
    }
}
