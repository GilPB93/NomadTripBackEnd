<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 68)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToOne(mappedBy: 'roleID', cascade: ['persist', 'remove'])]
    private ?Admin $adminID = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAdminID(): ?Admin
    {
        return $this->adminID;
    }

    public function setAdminID(Admin $adminID): static
    {
        // set the owning side of the relation if necessary
        if ($adminID->getRoleID() !== $this) {
            $adminID->setRoleID($this);
        }

        $this->adminID = $adminID;

        return $this;
    }
}
