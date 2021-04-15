<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=254, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;



    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $register;

    /**
     * @ORM\Column(type="datetime")
     */
    private $subscribe;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

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
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
        $this->messagesSend = new ArrayCollection();
        $this->messagesReceive = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }



    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles) 
    {
        switch($roles[0])
        {
            case "1": // Locataire
                $this->roles = ["ROLE_LOCATAIRE"];
                break;
            case "2": // Propriétaire
                $this->roles = ["ROLE_LOCATAIRE","ROLE_PROPRIETAIRE"];
                break;
            case "3": // Bailleur Tiers
                $this->roles = ["ROLE_LOCATAIRE","ROLE_PROPRIETAIRE","ROLE_BAILLEUR_TIERS"];
                break;
            case "4": // Agence
                $this->roles = ["ROLE_LOCATAIRE","ROLE_PROPRIETAIRE","ROLE_BAILLEUR_TIERS","ROLE_AGENCE"];
                break;
        }
        return $this;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getRegister(): ?\DateTimeInterface
    {
        return $this->register;
    }

    public function setRegister(\DateTimeInterface $register): self
    {
        $this->register = $register;

        return $this;
    }

    public function getSubscribe(): ?\DateTimeInterface
    {
        return $this->subscribe;
    }

    public function setSubscribe(\DateTimeInterface $subscribe): self
    {
        $this->subscribe = $subscribe;

        return $this;
    }

}
