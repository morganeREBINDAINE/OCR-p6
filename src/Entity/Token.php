<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 */
class Token
{
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_FORGOTTEN_PASSWORD = 'forgotten_password';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $accessed;

    public function __construct(string $type, User $user)
    {
        $this->token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $this->user = $user;
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAccessed(): ?\DateTimeInterface
    {
        return $this->accessed;
    }

    public function setAccessed(?\DateTimeInterface $accessed): self
    {
        $this->accessed = $accessed;

        return $this;
    }
}
