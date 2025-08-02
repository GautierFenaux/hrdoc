<?php

namespace App\Entity;

use App\Repository\ManagerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ManagerRepository::class)]
class Manager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $departement = null;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: TeletravailForm::class, cascade: ['remove'])]
    private Collection $teletravailform;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: Cet::class)]
    private Collection $cet;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: RetourSurSite::class, cascade: ['remove'])]
    private Collection $retourSurSites;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $relance_teletravail = null;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: Astreinte::class)]
    private Collection $astreintes;
    
    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->teletravailform = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->cet = new ArrayCollection();
        $this->retourSurSites = new ArrayCollection();
        $this->astreintes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

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

    /**
     * @return Collection<int, TeletravailForm>
     */
    public function getTeletravailform(): Collection
    {
        return $this->teletravailform;
    }

    public function addTeletravailform(TeletravailForm $teletravailform): static
    {
        if (!$this->teletravailform->contains($teletravailform)) {
            $this->teletravailform->add($teletravailform);
            $teletravailform->setManager($this);
        }

        return $this;
    }

    public function removeTeletravailform(TeletravailForm $teletravailform): static
    {
        if ($this->teletravailform->removeElement($teletravailform)) {
            // set the owning side to null (unless already changed)
            if ($teletravailform->getManager() === $this) {
                $teletravailform->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setManager($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getManager() === $this) {
                $user->setManager(null);
            }
        }

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
            $cet->setManager($this);
        }

        return $this;
    }

    public function removeCet(Cet $cet): static
    {
        if ($this->cet->removeElement($cet)) {
            // set the owning side to null (unless already changed)
            if ($cet->getManager() === $this) {
                $cet->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RetourSurSite>
     */
    public function getRetourSurSites(): Collection
    {
        return $this->retourSurSites;
    }

    public function addRetourSurSite(RetourSurSite $retourSurSite): static
    {
        if (!$this->retourSurSites->contains($retourSurSite)) {
            $this->retourSurSites->add($retourSurSite);
            $retourSurSite->setManager($this);
        }

        return $this;
    }

    public function removeRetourSurSite(RetourSurSite $retourSurSite): static
    {
        if ($this->retourSurSites->removeElement($retourSurSite)) {
            // set the owning side to null (unless already changed)
            if ($retourSurSite->getManager() === $this) {
                $retourSurSite->setManager(null);
            }
        }

        return $this;
    }

    public function getRelanceTeletravail(): ?\DateTimeInterface
    {
        return $this->relance_teletravail;
    }

    public function setRelanceTeletravail(?\DateTimeInterface $relance_teletravail): static
    {
        $this->relance_teletravail = $relance_teletravail;

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
            $astreinte->setManager($this);
        }

        return $this;
    }

    public function removeAstreinte(Astreinte $astreinte): static
    {
        if ($this->astreintes->removeElement($astreinte)) {
            // set the owning side to null (unless already changed)
            if ($astreinte->getManager() === $this) {
                $astreinte->setManager(null);
            }
        }

        return $this;
    }
   
}
