<?php

namespace App\Entity;

use App\Repository\ListPlacesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListPlacesRepository::class)]
class ListPlaces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $visiteDateTime = null;

    #[ORM\OneToOne(mappedBy: 'listPlaces', cascade: ['persist', 'remove'])]
    private ?Travelbook $travelbook = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getVisiteDateTime(): ?\DateTimeInterface
    {
        return $this->visiteDateTime;
    }

    public function setVisiteDateTime(\DateTimeInterface $visiteDateTime): static
    {
        $this->visiteDateTime = $visiteDateTime;

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
            $this->travelbook->setListPlaces(null);
        }

        // set the owning side of the relation if necessary
        if ($travelbook !== null && $travelbook->getListPlaces() !== $this) {
            $travelbook->setListPlaces($this);
        }

        $this->travelbook = $travelbook;

        return $this;
    }
}
