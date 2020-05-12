<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A trait for User identity in Opinion and Reduction entities.
 *
 * Private instead of Protected because of a well know behaviour from Doctrine --regenerate.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
trait EntityUserIdentityTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(
     *     max=64,
     *     maxMessage="validator.user_identity.name.max_length"
     * )
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Email(
     *     message="validator.user_identity.email.email"
     * )
     * @Assert\Length(
     *     max=64,
     *     maxMessage="validator.user_identity.email.max_length"
     * )
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $clientIp;

    public function __toString(): string
    {
        return $this->getName().' : '.$this->clientIp;
    }

    public function getName(): ?string
    {
        return $this->name ?: $this->clientIp;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    public function setClientIp(?string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }
}
