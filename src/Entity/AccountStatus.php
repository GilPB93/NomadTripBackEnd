<?php

namespace App\Entity;

use App\Repository\AccountStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountStatusRepository::class)]
class AccountStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\OneToOne(mappedBy: 'userStatus', cascade: ['persist', 'remove'])]
    private ?User $userStatus = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $validStatus = ['Active', 'Inactive'];

        if (!in_array($status, $validStatus)) {
            throw new \InvalidArgumentException("Invalid status name: $status");
        }

        $this->status = $status;
        return $this;
    }

    public function getUserStatus(): ?User
    {
        return $this->userStatus;
    }

    public function setUserStatus(User $userStatus): static
    {
        // set the owning side of the relation if necessary
        if ($userStatus->getUserStatus() !== $this) {
            $userStatus->setUserStatus($this);
        }

        $this->userStatus = $userStatus;

        return $this;
    }

}
