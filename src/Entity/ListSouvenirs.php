<?php

namespace App\Entity;

use App\Repository\ListSouvenirsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListSouvenirsRepository::class)]
class ListSouvenirs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $forWho = null;

    #[ORM\Column(length: 255)]
    private ?string $what = null;

    #[ORM\OneToOne(mappedBy: 'listSouvenirs', cascade: ['persist', 'remove'])]
    private ?Travelbook $travelbook = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForWho(): ?string
    {
        return $this->forWho;
    }

    public function setForWho(string $forWho): static
    {
        $this->forWho = $forWho;

        return $this;
    }

    public function getWhat(): ?string
    {
        return $this->what;
    }

    public function setWhat(string $what): static
    {
        $this->what = $what;

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
            $this->travelbook->setListSouvenirs(null);
        }

        // set the owning side of the relation if necessary
        if ($travelbook !== null && $travelbook->getListSouvenirs() !== $this) {
            $travelbook->setListSouvenirs($this);
        }

        $this->travelbook = $travelbook;

        return $this;
    }
}
