<?php

namespace App\Entity;

use JsonSerializable;
use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\EntityTimeTrait;
use App\Entity\Traits\EntityUserIdentityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OpinionRepository")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Opinion implements JsonSerializable
{
    use EntityIdTrait;
    use EntityTimeTrait;
    use EntityUserIdentityTrait;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="opinions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var Reduction
     *
     * @ORM\ManyToOne(targetEntity="Reduction", inversedBy="opinions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reduction;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="validator.opinion.comment.not_blank")
     * @Assert\Length(
     *     min=5,
     *     minMessage="validator.opinion.comment.min_length",
     *     max=10000,
     *     maxMessage="validator.opinion.comment.max_length"
     * )
     */
    private $comment;

    /* Personal methods */

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): string
    {
        // http://php.net/manual/en/class.jsonserializable.php
        // e.g. opinions|json_encode
        return $this->comment;
    }

    /* Auto generated methods */

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    public function setReduction(Reduction $reduction): self
    {
        $this->reduction = $reduction;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }
}
