<?php

namespace App\Entity;

use App\Repository\TravelbookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TravelbookRepository::class)]
class Travelbook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $departureAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $comebackAt = null;

    #[ORM\Column(length: 28, nullable: true)]
    private ?string $flightNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accommodation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(inversedBy: 'travelbook', cascade: ['persist', 'remove'])]
    private ?ListPlaces $listPlaces = null;

    #[ORM\OneToOne(inversedBy: 'travelbook', cascade: ['persist', 'remove'])]
    private ?ListFB $listFB = null;

    #[ORM\OneToOne(inversedBy: 'travelbook', cascade: ['persist', 'remove'])]
    private ?ListPhotos $listPhotos = null;

    #[ORM\OneToOne(inversedBy: 'travelbook', cascade: ['persist', 'remove'])]
    private ?ListSouvenirs $listSouvenirs = null;

    #[ORM\ManyToOne(inversedBy: 'travelbooks')]
    private ?User $userTravelbooks = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDepartureAt(): ?\DateTimeImmutable
    {
        return $this->departureAt;
    }

    public function setDepartureAt(\DateTimeImmutable $departureAt): static
    {
        $this->departureAt = $departureAt;

        return $this;
    }

    public function getComebackAt(): ?\DateTimeImmutable
    {
        return $this->comebackAt;
    }

    public function setComebackAt(\DateTimeImmutable $comebackAt): static
    {
        $this->comebackAt = $comebackAt;

        return $this;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(?string $flightNumber): static
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    public function getAccommodation(): ?string
    {
        return $this->accommodation;
    }

    public function setAccommodation(?string $accommodation): static
    {
        $this->accommodation = $accommodation;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getListPlaces(): ?ListPlaces
    {
        return $this->listPlaces;
    }

    public function setListPlaces(?ListPlaces $listPlaces): static
    {
        $this->listPlaces = $listPlaces;

        return $this;
    }

    public function getListFB(): ?ListFB
    {
        return $this->listFB;
    }

    public function setListFB(?ListFB $listFB): static
    {
        $this->listFB = $listFB;

        return $this;
    }

    public function getListPhotos(): ?ListPhotos
    {
        return $this->listPhotos;
    }

    public function setListPhotos(?ListPhotos $listPhotos): static
    {
        $this->listPhotos = $listPhotos;

        return $this;
    }

    public function getListSouvenirs(): ?ListSouvenirs
    {
        return $this->listSouvenirs;
    }

    public function setListSouvenirs(?ListSouvenirs $listSouvenirs): static
    {
        $this->listSouvenirs = $listSouvenirs;

        return $this;
    }

    public function getUserTravelbooks(): ?User
    {
        return $this->userTravelbooks;
    }

    public function setUserTravelbooks(?User $userTravelbooks): static
    {
        $this->userTravelbooks = $userTravelbooks;

        return $this;
    }
}
