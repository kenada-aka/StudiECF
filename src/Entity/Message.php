<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesSend")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesReceive")
     * @ORM\JoinColumn(nullable=true)
     */
    private $id_receiver;

    /**
     * @ORM\ManyToOne(targetEntity=Realty::class, inversedBy="messagesRealty")
     * @ORM\JoinColumn(nullable=true)
     */
    private $id_owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $type; // 1 = problème, 2 = simple message, 3 = travaux, 4 = échelonner le loyer

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSender(): ?User
    {
        return $this->id_sender;
    }

    public function setIdSender(?User $id_sender): self
    {
        $this->id_sender = $id_sender;

        return $this;
    }

    public function getIdReceiver(): ?User
    {
        return $this->id_receiver;
    }
    

    public function setIdReceiver(?User $id_receiver): self
    {
        $this->id_receiver = $id_receiver;

        return $this;
    }

    public function getIdOwner(): ?Realty
    {
        return $this->id_owner;
    }

    public function setIdOwner(?Realty $id_owner): self
    {
        $this->id_owner = $id_owner;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
