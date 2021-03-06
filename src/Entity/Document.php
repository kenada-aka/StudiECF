<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document
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
    private $url;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $askRemove;

    /**
     * @ORM\ManyToOne(targetEntity=Realty::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_realty;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAskRemove(): ?bool
    {
        return $this->askRemove;
    }

    public function setAskRemove(bool $askRemove): self
    {
        $this->askRemove = $askRemove;

        return $this;
    }

    public function getIdRealty(): ?Realty
    {
        return $this->id_realty;
    }

    public function setIdRealty(?Realty $id_realty): self
    {
        $this->id_realty = $id_realty;

        return $this;
    }
}
