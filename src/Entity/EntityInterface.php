<?php

declare(strict_types=1);

namespace App\Entity;

use \DateTimeImmutable;
use \DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * This interface signals that all entities needs Trait methods to save them according to the business logic.
 * Not needed in PHP 7.4, this interface is created for the lack of covariance.
 *
 * @see     https://devalmonte.com/blog/2019-04-18/interfaces-in-php-dont-make-complete-sense/
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
interface EntityInterface
{
    public function getId(): ?int;

    public function getUuid(): UuidInterface;

    public function setUuid(UuidInterface $uuid): void;

    public function getCreatedAt(): ?DateTimeImmutable;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $updatedAt): void;

    public function setUpdatedAt(?DateTimeInterface $updatedAt): void;
}
