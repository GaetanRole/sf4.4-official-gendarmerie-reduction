<?php

namespace App\Entity;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * This interface signals that all entities needs Trait methods to save them later according to the business logic.
 * Not needed in PHP 7.4, this interface is created for the lack of covariance.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
interface EntityInterface
{
    public function setUuid(UuidInterface $uuid): void;
    public function setCreatedAt(DateTimeInterface $updatedAt): void;
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void;
}
