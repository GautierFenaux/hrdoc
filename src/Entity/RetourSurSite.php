<?php

namespace App\Entity;

use App\Repository\RetourSurSiteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetourSurSiteRepository::class)]
class RetourSurSite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinTeletravail = null;

    #[ORM\Column(nullable: true)]
    private ?bool $autonomieInsuffisante = null;

    #[ORM\Column(nullable: true)]
    private ?bool $problemesConnexion = null;

    #[ORM\Column(nullable: true)]
    private ?bool $collaborateurInjoignable = null;

    #[ORM\Column(nullable: true)]
    private ?bool $diminutionProductivite = null;

    #[ORM\Column(nullable: true)]
    private ?bool $desorganiseService = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $autres = null;

    #[ORM\ManyToOne(inversedBy: 'retourSurSites')]
    private ?Manager $manager = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signatureRh = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signatureCollab = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSignatureRh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    private ?bool $entretienRh = null;

    #[ORM\ManyToOne(inversedBy: 'retourSurSite')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFinTeletravail(): ?\DateTimeInterface
    {
        return $this->dateFinTeletravail;
    }

    public function setDateFinTeletravail(\DateTimeInterface $dateFinTeletravail): static
    {
        $this->dateFinTeletravail = $dateFinTeletravail;

        return $this;
    }

    public function isAutonomieInsuffisante(): ?bool
    {
        return $this->autonomieInsuffisante;
    }

    public function setAutonomieInsuffisante(?bool $autonomieInsuffisante): static
    {
        $this->autonomieInsuffisante = $autonomieInsuffisante;

        return $this;
    }

    public function isProblemesConnexion(): ?bool
    {
        return $this->problemesConnexion;
    }

    public function setProblemesConnexion(?bool $problemesConnexion): static
    {
        $this->problemesConnexion = $problemesConnexion;

        return $this;
    }

    public function isCollaborateurInjoignable(): ?bool
    {
        return $this->collaborateurInjoignable;
    }

    public function setCollaborateurInjoignable(?bool $collaborateurInjoignable): static
    {
        $this->collaborateurInjoignable = $collaborateurInjoignable;

        return $this;
    }

    public function isDiminutionProductivite(): ?bool
    {
        return $this->diminutionProductivite;
    }

    public function setDiminutionProductivite(?bool $diminutionProductivite): static
    {
        $this->diminutionProductivite = $diminutionProductivite;

        return $this;
    }

    public function isDesorganiseService(): ?bool
    {
        return $this->desorganiseService;
    }

    public function setDesorganiseService(?bool $desorganiseService): static
    {
        $this->desorganiseService = $desorganiseService;

        return $this;
    }

    public function getAutres(): ?string
    {
        return $this->autres;
    }

    public function setAutres(?string $autres): static
    {
        $this->autres = $autres;

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

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function isSignatureRh(): ?bool
    {
        return $this->signatureRh;
    }

    public function setSignatureRh(?bool $signatureRh): static
    {
        $this->signatureRh = $signatureRh;

        return $this;
    }

    public function isSignatureCollab(): ?bool
    {
        return $this->signatureCollab;
    }

    public function setSignatureCollab(?bool $signatureCollab): static
    {
        $this->signatureCollab = $signatureCollab;

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

    public function getDateSignatureRh(): ?\DateTimeInterface
    {
        return $this->dateSignatureRh;
    }

    public function setDateSignatureRh(?\DateTimeInterface $dateSignatureRh): static
    {
        $this->dateSignatureRh = $dateSignatureRh;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function isEntretienRh(): ?bool
    {
        return $this->entretienRh;
    }

    public function setEntretienRh(?bool $entretienRh): static
    {
        $this->entretienRh = $entretienRh;

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
    

}
