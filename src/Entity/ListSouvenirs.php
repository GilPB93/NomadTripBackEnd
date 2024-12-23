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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $what = null;

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

    public function setWhat(?string $what): static
    {
        $this->what = $what;

        return $this;
    }
}
