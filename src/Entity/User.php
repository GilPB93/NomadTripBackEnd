<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 128)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 56)]
    private ?string $pseudo = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $apiToken;

    #[ORM\OneToOne(inversedBy: 'userStatus', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountStatus $userStatus = null;

    /**
     * @var Collection<int, ContactMessages>
     */
    #[ORM\OneToMany(targetEntity: ContactMessages::class, mappedBy: 'userContactMessages')]
    private Collection $contactMessages;

    /**
     * @var Collection<int, ActivityLog>
     */
    #[ORM\OneToMany(targetEntity: ActivityLog::class, mappedBy: 'userActivityLog')]
    private Collection $userActivityLog;

    /**
     * @var Collection<int, Travelbook>
     */
    #[ORM\OneToMany(targetEntity: Travelbook::class, mappedBy: 'userTravelbooks')]
    private Collection $travelbooks;

    /** @throws \Exception */
    public function __construct()
    {
        $this->apiToken = bin2hex(random_bytes(20));
        $this->contactMessages = new ArrayCollection();
        $this->userActivityLog = new ArrayCollection();
        $this->travelbooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

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

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getUserStatus(): ?AccountStatus
    {
        return $this->userStatus;
    }

    public function setUserStatus(AccountStatus $userStatus): static
    {
        $this->userStatus = $userStatus;

        return $this;
    }

    /**
     * @return Collection<int, ContactMessages>
     */
    public function getContactMessages(): Collection
    {
        return $this->contactMessages;
    }

    public function addContactMessage(ContactMessages $contactMessage): static
    {
        if (!$this->contactMessages->contains($contactMessage)) {
            $this->contactMessages->add($contactMessage);
            $contactMessage->setUserContactMessages($this);
        }

        return $this;
    }

    public function removeContactMessage(ContactMessages $contactMessage): static
    {
        if ($this->contactMessages->removeElement($contactMessage)) {
            // set the owning side to null (unless already changed)
            if ($contactMessage->getUserContactMessages() === $this) {
                $contactMessage->setUserContactMessages(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    public function getUserActivityLog(): Collection
    {
        return $this->userActivityLog;
    }

    public function addUserActivityLog(ActivityLog $userActivityLog): static
    {
        if (!$this->userActivityLog->contains($userActivityLog)) {
            $this->userActivityLog->add($userActivityLog);
            $userActivityLog->setUserActivityLog($this);
        }

        return $this;
    }

    public function removeUserActivityLog(ActivityLog $userActivityLog): static
    {
        if ($this->userActivityLog->removeElement($userActivityLog)) {
            // set the owning side to null (unless already changed)
            if ($userActivityLog->getUserActivityLog() === $this) {
                $userActivityLog->setUserActivityLog(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Travelbook>
     */
    public function getTravelbooks(): Collection
    {
        return $this->travelbooks;
    }

    public function addTravelbook(Travelbook $travelbook): static
    {
        if (!$this->travelbooks->contains($travelbook)) {
            $this->travelbooks->add($travelbook);
            $travelbook->setUserTravelbooks($this);
        }

        return $this;
    }

    public function removeTravelbook(Travelbook $travelbook): static
    {
        if ($this->travelbooks->removeElement($travelbook)) {
            // set the owning side to null (unless already changed)
            if ($travelbook->getUserTravelbooks() === $this) {
                $travelbook->setUserTravelbooks(null);
            }
        }

        return $this;
    }



}
