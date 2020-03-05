<?php

declare(strict_types = 1);

namespace App\Entity\Traits;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * A trait for id and uuid properties in every entities.
 * You can also use mappedsuperclass but not recommended.
 *
 * @link    https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html
 * @link    https://stackoverflow.com/questions/25749418/symfony2-mappedsuperclass-and-doctrinegenerateentities
 *
 * Private instead of Protected because of a well know behaviour from Doctrine --regenerate.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
trait EntityIdTrait
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * The public primary identity key.
     *
     * @var UuidInterface
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    private $uuid;

    /* Auto generated methods */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->uuid = $uuid;
    }
}
