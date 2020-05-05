<?php

declare(strict_types=1);

namespace App\Repository\Adapter;

use App\Entity\EntityInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * This interface signals that all repositories needs a save, update, and delete method according to the business logic.
 * Interface method declarations can include default argument values.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
interface RepositoryAdapterInterface
{
    public function getRepository(string $className): ObjectRepository;

    public function save(EntityInterface $entity, string $notificationKey = 'save.flash.success'): EntityInterface;

    public function update(EntityInterface $entity, string $notificationKey = 'update.flash.success'): EntityInterface;

    public function delete(EntityInterface $entity, string $notificationKey = 'delete.flash.success'): void;
}
