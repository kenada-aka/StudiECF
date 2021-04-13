<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $actor;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="id_sender")
     */
    private $messagesSend;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="id_receiver")
     */
    private $messagesReceive;

    /**
     * @ORM\OneToOne(targetEntity=Realty::class, mappedBy="id_owner", cascade={"persist", "remove"})
     */
    private $realtyOwner;

    /**
     * @ORM\OneToOne(targetEntity=Realty::class, mappedBy="id_tenant", cascade={"persist", "remove"})
     */
    private $realtyTenant;

    public function __construct()
    {
        $this->messagesSend = new ArrayCollection();
        $this->messagesReceive = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }



    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getActor(): ?string
    {
        return $this->actor;
    }

    public function setActor(string $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesSend(): Collection
    {
        return $this->messagesSend;
    }

    public function addMessagesSend(Message $messagesSend): self
    {
        if (!$this->messagesSend->contains($messagesSend)) {
            $this->messagesSend[] = $messagesSend;
            $messagesSend->setIdSender($this);
        }

        return $this;
    }

    public function removeMessagesSend(Message $messagesSend): self
    {
        if ($this->messagesSend->removeElement($messagesSend)) {
            // set the owning side to null (unless already changed)
            if ($messagesSend->getIdSender() === $this) {
                $messagesSend->setIdSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesReceive(): Collection
    {
        return $this->messagesReceive;
    }

    public function addMessagesReceive(Message $messagesReceive): self
    {
        if (!$this->messagesReceive->contains($messagesReceive)) {
            $this->messagesReceive[] = $messagesReceive;
            $messagesReceive->setIdReceiver($this);
        }

        return $this;
    }

    public function removeMessagesReceive(Message $messagesReceive): self
    {
        if ($this->messagesReceive->removeElement($messagesReceive)) {
            // set the owning side to null (unless already changed)
            if ($messagesReceive->getIdReceiver() === $this) {
                $messagesReceive->setIdReceiver(null);
            }
        }

        return $this;
    }

    public function getRealtyOwner(): ?Realty
    {
        return $this->realtyOwner;
    }

    public function setRealtyOwner(Realty $realtyOwner): self
    {
        // set the owning side of the relation if necessary
        if ($realtyOwner->getIdOwner() !== $this) {
            $realtyOwner->setIdOwner($this);
        }

        $this->realtyOwner = $realtyOwner;

        return $this;
    }

    public function getRealtyTenant(): ?Realty
    {
        return $this->realtyTenant;
    }

    public function setRealtyTenant(?Realty $realtyTenant): self
    {
        // unset the owning side of the relation if necessary
        if ($realtyTenant === null && $this->realtyTenant !== null) {
            $this->realtyTenant->setIdTenant(null);
        }

        // set the owning side of the relation if necessary
        if ($realtyTenant !== null && $realtyTenant->getIdTenant() !== $this) {
            $realtyTenant->setIdTenant($this);
        }

        $this->realtyTenant = $realtyTenant;

        return $this;
    }

}
