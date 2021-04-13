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
     * @ORM\Column(type="boolean")
     */
    private $to_rent;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="id_realty")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="id_realty")
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="realtyOwner", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_owner;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="realtyTenant", cascade={"persist", "remove"})
     */
    private $id_tenant;

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

    public function getToRent(): ?bool
    {
        return $this->to_rent;
    }

    public function setToRent(bool $to_rent): self
    {
        $this->to_rent = $to_rent;

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
                $document->setIdRealty(null);
            }
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
                $image->setIdRealty(null);
            }
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
    
}
