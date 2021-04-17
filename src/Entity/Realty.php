<?php

namespace App\Entity;

use App\Repository\RealtyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RealtyRepository::class)
 */
class Realty
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $rent;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut; // 1 = private propriétaire, 2 = private bailleur tiers ou agence, 3 = publique libre de location, 4 = réserver par locataire, 5 = louer

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="id_realty", orphanRemoval=true)
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="id_realty", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="realtyOwner")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $id_owner;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="realtyTenant")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $id_tenant;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="id_owner", orphanRemoval=true)
     */
    private $messagesRealty;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="realtiesAgency")
     */
    private $id_agency;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRent(): ?int
    {
        return $this->rent;
    }

    public function setRent(int $rent): self
    {
        $this->rent = $rent;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesRealty(): Collection
    {
        return $this->messagesRealty;
    }

    public function addMessagesRealty(Message $messagesRealty): self
    {
        if (!$this->messagesRealty->contains($messagesRealty)) {
            $this->messagesRealty[] = $messagesRealty;
            $messagesRealty->setIdRealty($this);
        }

        return $this;
    }

    public function removeMessagesRealty(Message $messagesRealty): self
    {
        if ($this->messagesRealty->removeElement($messagesRealty)) {
            // set the owning side to null (unless already changed)
            if ($messagesRealty->getIdRealty() === $this) {
                $messagesRealty->setIdRealty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setIdRealty($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getIdRealty() === $this) {
                //$document->setIdRealty(null);
            }
        }

        return $this;
    }

    public function removeAllDocuments($path): self
    {
        $documents = $this->getDocuments();
        
        foreach($documents as $document)
        {
            $file = $path ."/". $document->getUrl();
            if(file_exists($file)) {
                unlink($file);
            }   
            $this->removeDocument($document);
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setIdRealty($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getIdRealty() === $this) {
                //$image->setIdRealty(null);
            }
        }

        return $this;
    }

    public function removeAllImages($path): self
    {
        $images = $this->getImages();
        
        foreach($images as $image)
        {
            $file = $path ."/". $image->getUrl();
            if(file_exists($file)) {
                unlink($file);
            }   
            $this->removeImage($image);
        }

        return $this;
    }

    public function getIdOwner(): ?User
    {
        return $this->id_owner;
    }

    public function setIdOwner(User $id_owner): self
    {
        $this->id_owner = $id_owner;

        return $this;
    }

    public function getIdTenant(): ?User
    {
        return $this->id_tenant;
    }

    public function setIdTenant(?User $id_tenant): self
    {
        $this->id_tenant = $id_tenant;

        return $this;
    }

    public function getIdAgency(): ?User
    {
        return $this->id_agency;
    }

    public function setIdAgency(?User $id_agency): self
    {
        $this->id_agency = $id_agency;

        return $this;
    }
    
}
