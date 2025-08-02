<?php

namespace App\Entity;

use App\Repository\AstreinteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AstreinteRepository::class)]
class Astreinte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $debutAstreinte = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $finAstreinte = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $motif = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isSignatureCollab = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isOk = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRefusCollab = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isOkRh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRefusRh = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $collabValidationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $rhValidationDate = null;

    #[ORM\ManyToOne(inversedBy: 'astreintes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'astreintes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Manager $manager = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?int $tempsValorise = null;

    #[ORM\Column(nullable: true)]
    private ?array $plageHoraire = null;

    #[ORM\Column(nullable: true)]
    private ?array $tempsOperation = null;

    #[ORM\Column(nullable: true)]
    private ?array $tempsDejeuner = null;

    #[ORM\Column]
    private ?bool $astreinte = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $tempsInterventionJour = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $tempsInterventionNuit = null;

    #[ORM\Column(nullable: true)]
    private ?bool $repos = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $tempsMajore = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebutAstreinte(): ?\DateTimeInterface
    {
        return $this->debutAstreinte;
    }

    public function setDebutAstreinte(\DateTimeInterface $debutAstreinte): static
    {
        $this->debutAstreinte = $debutAstreinte;

        return $this;
    }

    public function getFinAstreinte(): ?\DateTimeInterface
    {
        return $this->finAstreinte;
    }

    public function setFinAstreinte(\DateTimeInterface $finAstreinte): static
    {
        $this->finAstreinte = $finAstreinte;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function isIsSignatureCollab(): ?bool
    {
        return $this->isSignatureCollab;
    }

    public function setIsSignatureCollab(?bool $isSignatureCollab): static
    {
        $this->isSignatureCollab = $isSignatureCollab;

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

    public function isOk(): ?bool
    {
        return $this->isOk;
    }

    public function setIsOk(?bool $isOk): static
    {
        $this->isOk = $isOk;

        return $this;
    }

    public function getMotifRefusCollab(): ?string
    {
        return $this->motifRefusCollab;
    }

    public function setMotifRefusCollab(?string $motifRefusCollab): static
    {
        $this->motifRefusCollab = $motifRefusCollab;

        return $this;
    }

    public function isOkRh(): ?bool
    {
        return $this->isOkRh;
    }

    public function setIsOkRh(?bool $isOkRh): static
    {
        $this->isOkRh = $isOkRh;

        return $this;
    }

    public function getMotifRefusRh(): ?string
    {
        return $this->motifRefusRh;
    }

    public function setMotifRefusRh(?string $motifRefusRh): static
    {
        $this->motifRefusRh = $motifRefusRh;

        return $this;
    }

    public function getCollabValidationDate(): ?\DateTimeInterface
    {
        return $this->collabValidationDate;
    }

    public function setCollabValidationDate(?\DateTimeInterface $collabValidationDate): static
    {
        $this->collabValidationDate = $collabValidationDate;

        return $this;
    }

    public function getRhValidationDate(): ?\DateTimeInterface
    {
        return $this->rhValidationDate;
    }

    public function setRhValidationDate(?\DateTimeInterface $rhValidationDate): static
    {
        $this->rhValidationDate = $rhValidationDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getTempsValorise(): ?int
    {
        return $this->tempsValorise;
    }

    public function setTempsValorise(?int $tempsValorise): static
    {
        $this->tempsValorise = $tempsValorise;

        return $this;
    }

    public function getPlageHoraire(): ?array
    {
        return $this->plageHoraire;
    }

    public function setPlageHoraire(?array $plageHoraire): static
    {
        $this->plageHoraire = $plageHoraire;

        return $this;
    }

    public function getTempsOperation(): ?array
    {
        return $this->tempsOperation;
    }

    public function setTempsOperation(?array $tempsOperation): static
    {
        $this->tempsOperation = $tempsOperation;

        return $this;
    }

    public function getTempsDejeuner(): ?array
    {
        return $this->tempsDejeuner;
    }

    public function setTempsDejeuner(?array $tempsDejeuner): static
    {
        $this->tempsDejeuner = $tempsDejeuner;

        return $this;
    }

    public function isAstreinte(): ?bool
    {
        return $this->astreinte;
    }

    public function setAstreinte(bool $astreinte): static
    {
        $this->astreinte = $astreinte;

        return $this;
    }

    public function getTempsInterventionJour(): ?array
    {
        return $this->tempsInterventionJour;
    }

    public function setTempsInterventionJour(?array $tempsInterventionJour): static
    {
        $this->tempsInterventionJour = $tempsInterventionJour;

        return $this;
    }

    public function getTempsInterventionNuit(): ?array
    {
        return $this->tempsInterventionNuit;
    }

    public function setTempsInterventionNuit(?array $tempsInterventionNuit): static
    {
        $this->tempsInterventionNuit = $tempsInterventionNuit;

        return $this;
    }

    public function isRepos(): ?bool
    {
        return $this->repos;
    }

    public function setRepos(?bool $repos): static
    {
        $this->repos = $repos;

        return $this;
    }

    public function getTempsMajore(): ?array
    {
        return $this->tempsMajore;
    }

    public function setTempsMajore(?array $tempsMajore): static
    {
        $this->tempsMajore = $tempsMajore;

        return $this;
    }
}
