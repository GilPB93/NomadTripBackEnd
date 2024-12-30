<?php

namespace App\Entity;

use App\Repository\ListFBRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListFBRepository::class)]
class ListFB
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
    private ?\DateTimeInterface $visitDateTime = null;

    #[ORM\OneToOne(mappedBy: 'listFB', cascade: ['persist', 'remove'])]
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

    public function getVisitDateTime(): ?\DateTimeInterface
    {
        return $this->visitDateTime;
    }

    public function setVisitDateTime(\DateTimeInterface $visitDateTime): static
    {
        $this->visitDateTime = $visitDateTime;

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
            $this->travelbook->setListFB(null);
        }

        // set the owning side of the relation if necessary
        if ($travelbook !== null && $travelbook->getListFB() !== $this) {
            $travelbook->setListFB($this);
        }

        $this->travelbook = $travelbook;

        return $this;
    }
}
