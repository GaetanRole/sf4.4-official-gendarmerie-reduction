<?php

/**
 * Entity Trait File
 *
 * PHP Version 7.2
 *
 * @category    Trait
 * @package     App\Entity\Traits
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait Class
 *
 * You can also use mappedsuperclass but not recommended.
 * @link        https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html
 * @link        https://stackoverflow.com/questions/25749418/symfony2-mappedsuperclass-and-doctrinegenerateentities
 *
 * Private instead of Protected because of a well know behaviour from Doctrine --regenerate.
 *
 * @category    Trait
 * @package     App\Entity\Traits
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
trait UserIdentityTrait
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

    /*
     * Personal methods
     */

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() . ' : ' .  $this->clientIp;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     */
    public function setClientIp(string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }
}
