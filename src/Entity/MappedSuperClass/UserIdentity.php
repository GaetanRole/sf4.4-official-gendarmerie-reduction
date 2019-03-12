<?php

/**
 * Mapped Super Class File
 *
 * PHP Version 7.2
 *
 * @category    Mapped
 * @package     App\Entity\MappedSuperClass
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Entity\MappedSuperClass;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserIdentity Class
 *
 * @ORM\MappedSuperclass()
 *
 * @link        https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html
 * @link        https://stackoverflow.com/questions/25749418/symfony2-mappedsuperclass-and-doctrinegenerateentities
 * Private instead of Protected because of a well know behaviour from Doctrine --regenerate.
 *
 * @category    User
 * @package     App\Entity\MappedSuperClass
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class UserIdentity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(
     *     max=64,
     *     maxMessage="Votre nom est bien trop long ! ({{ limit }} max)."
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Email(
     *     message = "L'adresse e-mail '{{ value }}' n'est pas valide. Veuillez vérifier celle-ci."
     * )
     * @Assert\Length(
     *     max=64,
     *     maxMessage="Votre email est bien trop long ! ({{ limit }} max)."
     * )
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $clientIp;

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
