<?php

namespace App\Entity;

use App\Enum\StateEnum;
use App\Repository\CetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CetRepository::class)]
class Cet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbJours = null;

    #[ORM\Column(nullable: true)]
    private ?int $solde = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbJoursADebiter = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $priseCetDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $priseCetFin = null;

    #[ORM\Column(nullable: true)]
    private ?int $droitCongesCumule = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbJoursCongesUtilises = null;

    #[ORM\Column(nullable: true)]
    private ?int $soldeJoursCongesNonPris = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbJoursVersement = null;

    #[ORM\Column(nullable: true)]
    private ?bool $avisSupHierarchique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaireSupHierarchique = null;

    #[ORM\Column(nullable: true)]
    private ?bool $avisDrh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaireDrh = null;

    #[ORM\ManyToOne(inversedBy: 'cet')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'cet')]
    #[ORM\JoinColumn(name: 'manager_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Manager $manager = null;

    #[ORM\Column(nullable: true)]
    private ?bool $alimentation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $restitution = null;

    #[ORM\Column(nullable: true)]
    private ?bool $utilisation = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbJoursLiquide = null;

    #[ORM\Column(nullable: true)]
    private ?int $TotalLiquidation = null;

    #[ORM\Column(length: 50, enumType: StateEnum::class)]
    private ?StateEnum $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signatureProfilCollab = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbJours(): ?int
    {
        return $this->nbJours;
    }

    public function setNbJours(?int $nbJours): static
    {
        $this->nbJours = $nbJours;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(?int $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    public function getNbJoursADebiter(): ?int
    {
        return $this->nbJoursADebiter;
    }

    public function setNbJoursADebiter(?int $nbJoursADebiter): static
    {
        $this->nbJoursADebiter = $nbJoursADebiter;

        return $this;
    }

    public function getPriseCetDebut(): ?\DateTimeInterface
    {
        return $this->priseCetDebut;
    }

    public function setPriseCetDebut(?\DateTimeInterface $priseCetDebut): static
    {
        $this->priseCetDebut = $priseCetDebut;

        return $this;
    }

    public function getPriseCetFin(): ?\DateTimeInterface
    {
        return $this->priseCetFin;
    }

    public function setPriseCetFin(?\DateTimeInterface $priseCetFin): static
    {
        $this->priseCetFin = $priseCetFin;

        return $this;
    }

    public function getDroitCongesCumule(): ?int
    {
        return $this->droitCongesCumule;
    }

    public function setDroitCongesCumule(int $droitCongesCumule): static
    {
        $this->droitCongesCumule = $droitCongesCumule;

        return $this;
    }

    public function getNbJoursCongesUtilises(): ?int
    {
        return $this->nbJoursCongesUtilises;
    }

    public function setNbJoursCongesUtilises(?int $nbJoursCongesUtilises): static
    {
        $this->nbJoursCongesUtilises = $nbJoursCongesUtilises;

        return $this;
    }

    public function getSoldeJoursCongesNonPris(): ?int
    {
        return $this->soldeJoursCongesNonPris;
    }

    public function setSoldeJoursCongesNonPris(?int $soldeJoursCongesNonPris): static
    {
        $this->soldeJoursCongesNonPris = $soldeJoursCongesNonPris;

        return $this;
    }

    public function getNbJoursVersement(): ?int
    {
        return $this->nbJoursVersement;
    }

    public function setNbJoursVersement(?int $nbJoursVersement): static
    {
        $this->nbJoursVersement = $nbJoursVersement;

        return $this;
    }

    public function isAvisSupHierarchique(): ?bool
    {
        return $this->avisSupHierarchique;
    }

    public function setAvisSupHierarchique(?bool $avisSupHierarchique): static
    {
        $this->avisSupHierarchique = $avisSupHierarchique;

        return $this;
    }

    public function getCommentaireSupHierarchique(): ?string
    {
        return $this->commentaireSupHierarchique;
    }

    public function setCommentaireSupHierarchique(?string $commentaireSupHierarchique): static
    {
        $this->commentaireSupHierarchique = $commentaireSupHierarchique;

        return $this;
    }

    public function isAvisDrh(): ?bool
    {
        return $this->avisDrh;
    }

    public function setAvisDrh(?bool $avisDrh): static
    {
        $this->avisDrh = $avisDrh;

        return $this;
    }

    public function getCommentaireDrh(): ?string
    {
        return $this->commentaireDrh;
    }

    public function setCommentaireDrh(?string $commentaireDrh): static
    {
        $this->commentaireDrh = $commentaireDrh;

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

    public function isAlimentation(): ?bool
    {
        return $this->alimentation;
    }

    public function setAlimentation(?bool $alimentation): static
    {
        $this->alimentation = $alimentation;

        return $this;
    }

    public function isRestitution(): ?bool
    {
        return $this->restitution;
    }

    public function setRestitution(?bool $restitution): static
    {
        $this->restitution = $restitution;

        return $this;
    }

    public function isUtilisation(): ?bool
    {
        return $this->utilisation;
    }

    public function setUtilisation(?bool $utilisation): static
    {
        $this->utilisation = $utilisation;

        return $this;
    }

    public function getNbJoursLiquide(): ?int
    {
        return $this->nbJoursLiquide;
    }

    public function setNbJoursLiquide(?int $nbJoursLiquide): static
    {
        $this->nbJoursLiquide = $nbJoursLiquide;

        return $this;
    }

    public function getTotalLiquidation(): ?int
    {
        return $this->TotalLiquidation;
    }

    public function setTotalLiquidation(?int $TotalLiquidation): static
    {
        $this->TotalLiquidation = $TotalLiquidation;

        return $this;
    }

    public function getState(): ?StateEnum
    {
        return $this->state;
    }

    public function setState(StateEnum $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

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

    public function isSignatureProfilCollab(): ?bool
    {
        return $this->signatureProfilCollab;
    }

    public function setSignatureProfilCollab(?bool $signatureProfilCollab): static
    {
        $this->signatureProfilCollab = $signatureProfilCollab;

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
}
