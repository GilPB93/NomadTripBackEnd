<?php

namespace App\Entity;

use App\Repository\ListPhotosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListPhotosRepository::class)]
class ListPhotos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imageUrl = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\OneToOne(mappedBy: 'listPhotos', cascade: ['persist', 'remove'])]
    private ?Travelbook $travelbook = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getTravelbook(): ?Travelbook
    {
        return $this->travelbook;
    }

    public function setTravelbook(?Travelbook $travelbook): static
    {
        // unset the owning side of the relation if necessary
        if ($travelbook === null && $this->travelbook !== null) {
            $this->travelbook->setListPhotos(null);
        }

        // set the owning side of the relation if necessary
        if ($travelbook !== null && $travelbook->getListPhotos() !== $this) {
            $travelbook->setListPhotos($this);
        }

        $this->travelbook = $travelbook;

        return $this;
    }
}
