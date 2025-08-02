<?php

namespace App\Entity;

use App\Entity\Manager;
use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true, nullable: true)]
    private ?string $login = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $departement = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eligibleTT = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TeletravailForm::class)]
    private Collection $teletravailForms;

    #[ORM\ManyToOne(targetEntity: Manager::class)]
    #[ORM\JoinColumn(name: 'manager_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Manager $manager = null;

    #[ORM\Column(nullable: true)]
    private ?bool $actif = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Cet::class)]
    private Collection $cet;

    #[ORM\Column(nullable: true)]
    private ?bool $eligibleCet = null;

    #[ORM\Column(nullable: true)]
    private ?bool $firstConnection = null;

    #[ORM\Column(nullable: true)]
    private ?bool $downloadKit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $matricule = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $relanceTeletravail = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RetourSurSite::class)]
    private Collection $retourSurSite;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Astreinte::class)]
    private Collection $astreintes;

    #[ORM\Column(nullable: true)]
    private ?bool $forfaitHeure = null;

    public function __construct()
    {
        $this->teletravailForms = new ArrayCollection();
        $this->cet = new ArrayCollection();
        $this->retourSurSite = new ArrayCollection();
        $this->astreintes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() . ' ' . $this->getSurname();
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
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getMetier(): ?string
    {
        return $this->metier;
    }

    public function setMetier(?string $metier): static
    {
        $this->metier = $metier;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): static
    {
        $this->departement = $departement;

        return $this;
    }



    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function isEligibleTT(): ?bool
    {
        return $this->eligibleTT;
    }

    public function setEligibleTT(?bool $eligibleTT): static
    {
        $this->eligibleTT = $eligibleTT;

        return $this;
    }

    /**
     * @return Collection<int, TeletravailForm>
     */
    public function getTeletravailForms(): Collection
    {
        return $this->teletravailForms;
    }

    public function addTeletravailForm(TeletravailForm $teletravailForm): static
    {
        if (!$this->teletravailForms->contains($teletravailForm)) {
            $this->teletravailForms->add($teletravailForm);
            $teletravailForm->setUser($this);
        }

        return $this;
    }

    public function removeTeletravailForm(TeletravailForm $teletravailForm): static
    {
        if ($this->teletravailForms->removeElement($teletravailForm)) {
            // set the owning side to null (unless already changed)
            if ($teletravailForm->getUser() === $this) {
                $teletravailForm->setUser(null);
            }
        }

        return $this;
    }

    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function setManager(?Manager $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Cet>
     */
    public function getCet(): Collection
    {
        return $this->cet;
    }

    public function addCet(Cet $cet): static
    {
        if (!$this->cet->contains($cet)) {
            $this->cet->add($cet);
            $cet->setUser($this);
        }

        return $this;
    }

    public function removeCet(Cet $cet): static
    {
        if ($this->cet->removeElement($cet)) {
            // set the owning side to null (unless already changed)
            if ($cet->getUser() === $this) {
                $cet->setUser(null);
            }
        }

        return $this;
    }

    public function isEligibleCet(): ?bool
    {
        return $this->eligibleCet;
    }

    public function setEligibleCet(?bool $eligibleCet): static
    {
        $this->eligibleCet = $eligibleCet;

        return $this;
    }

    public function isFirstConnection(): ?bool
    {
        return $this->firstConnection;
    }

    public function setFirstConnection(?bool $firstConnection): static
    {
        $this->firstConnection = $firstConnection;

        return $this;
    }

    public function isDownloadKit(): ?bool
    {
        return $this->downloadKit;
    }

    public function setDownloadKit(?bool $downloadKit): static
    {
        $this->downloadKit = $downloadKit;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getRelanceTeletravail(): ?\DateTimeInterface
    {
        return $this->relanceTeletravail;
    }

    public function setRelanceTeletravail(?\DateTimeInterface $relanceTeletravail): static
    {
        $this->relanceTeletravail = $relanceTeletravail;

        return $this;
    }

    /**
     * @return Collection<int, RetourSurSite>
     */
    public function getRetourSurSite(): Collection
    {
        return $this->retourSurSite;
    }

    public function addRetourSurSite(RetourSurSite $retourSurSite): static
    {
        if (!$this->retourSurSite->contains($retourSurSite)) {
            $this->retourSurSite->add($retourSurSite);
            $retourSurSite->setUser($this);
        }

        return $this;
    }

    public function removeRetourSurSite(RetourSurSite $retourSurSite): static
    {
        if ($this->retourSurSite->removeElement($retourSurSite)) {
            // set the owning side to null (unless already changed)
            if ($retourSurSite->getUser() === $this) {
                $retourSurSite->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Astreinte>
     */
    public function getAstreintes(): Collection
    {
        return $this->astreintes;
    }

    public function addAstreinte(Astreinte $astreinte): static
    {
        if (!$this->astreintes->contains($astreinte)) {
            $this->astreintes->add($astreinte);
            $astreinte->setUser($this);
        }

        return $this;
    }

    public function removeAstreinte(Astreinte $astreinte): static
    {
        if ($this->astreintes->removeElement($astreinte)) {
            // set the owning side to null (unless already changed)
            if ($astreinte->getUser() === $this) {
                $astreinte->setUser(null);
            }
        }

        return $this;
    }

    public function isForfaitHeure(): ?bool
    {
        return $this->forfaitHeure;
    }

    public function setForfaitHeure(?bool $forfaitHeure): static
    {
        $this->forfaitHeure = $forfaitHeure;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
