<?php

namespace App\Entity;

use App\Enum\StateEnum;
use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeletravailFormRepository;

//TODO: revoir la cardinalité entre le user et le teletravailForm, si doit créer plusieurs
#[ORM\Entity(repositoryClass: TeletravailFormRepository::class)]
class TeletravailForm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $natureContrat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $quotite = null;

    #[ORM\Column(nullable: true)]
    private ?bool $avisManager = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaireManager = null;

    #[ORM\Column(nullable: true)]
    private ?bool $avisDRH = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaireDRH = null;

    #[ORM\Column(length: 50, enumType: StateEnum::class)]
    private ?StateEnum $state = null;

    #[ORM\ManyToOne(inversedBy: 'teletravailform')]
    private ?Manager $manager = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $document = null;

    #[ORM\Column]
    private ?bool $ConnexionInternet = null;

    #[ORM\Column(length: 255)]
    private ?string $attestationAssurance = null;

    #[ORM\Column]
    private array $journeesTeletravaillees = [];

    #[ORM\Column(nullable: true)]
    private ?bool $periodeEssai = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activiteEligible = null;

    #[ORM\Column(nullable: true)]
    private ?bool $autonomieSuffisante = null;

    #[ORM\Column(nullable: true)]
    private ?bool $conditionsEligibilites = null;

    #[ORM\Column(nullable: true)]
    private ?bool $conditionsTechMatAdm = null;

    #[ORM\Column(nullable: true)]
    private ?bool $desorganiseService = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $aCompterDu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinTeletravail = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuTeletravail = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $fonctionExercee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $receptionDemande = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $quotitePersonnel = null;

    #[ORM\ManyToOne(inversedBy: 'teletravailForms', cascade: ['persist'])]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $attestationHonneur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNatureContrat(): ?string
    {
        return $this->natureContrat;
    }

    public function setNatureContrat(?string $natureContrat): static
    {
        $this->natureContrat = $natureContrat;

        return $this;
    }

    public function getQuotite(): ?string
    {
        return $this->quotite;
    }

    public function setQuotite(?string $quotite): static
    {
        $this->quotite = $quotite;

        return $this;
    }

    public function isAvisManager(): ?bool
    {
        return $this->avisManager;
    }

    public function setAvisManager(?bool $avisManager): static
    {
        $this->avisManager = $avisManager;

        return $this;
    }

    public function getCommentaireManager(): ?string
    {
        return $this->commentaireManager;
    }

    public function setCommentaireManager(?string $commentaireManager): static
    {
        $this->commentaireManager = $commentaireManager;

        return $this;
    }

    public function isAvisDRH(): ?bool
    {
        return $this->avisDRH;
    }

    public function setAvisDRH(?bool $avisDRH): static
    {
        $this->avisDRH = $avisDRH;

        return $this;
    }

    public function getCommentaireDRH(): ?string
    {
        return $this->commentaireDRH;
    }

    public function setCommentaireDRH(?string $commentaireDRH): static
    {
        $this->commentaireDRH = $commentaireDRH;

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


    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function setManager(?Manager $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function isConnexionInternet(): ?bool
    {
        return $this->ConnexionInternet;
    }

    public function setConnexionInternet(bool $ConnexionInternet): static
    {
        $this->ConnexionInternet = $ConnexionInternet;

        return $this;
    }

    public function getAttestationAssurance(): ?string
    {
        return $this->attestationAssurance;
    }

    public function setAttestationAssurance(string $attestationAssurance): static
    {
        $this->attestationAssurance = $attestationAssurance;

        return $this;
    }

    public function getJourneesTeletravaillees(): array
    {
        return $this->journeesTeletravaillees;
    }

    public function setJourneesTeletravaillees(array $journeesTeletravaillees): static
    {
        $this->journeesTeletravaillees = $journeesTeletravaillees;

        return $this;
    }

    public function isPeriodeEssai(): ?bool
    {
        return $this->periodeEssai;
    }

    public function setPeriodeEssai(?bool $periodeEssai): static
    {
        $this->periodeEssai = $periodeEssai;

        return $this;
    }

    public function isActiviteEligible(): ?bool
    {
        return $this->activiteEligible;
    }

    public function setActiviteEligible(?bool $activiteEligible): static
    {
        $this->activiteEligible = $activiteEligible;

        return $this;
    }

    public function isAutonomieSuffisante(): ?bool
    {
        return $this->autonomieSuffisante;
    }

    public function setAutonomieSuffisante(bool|null $autonomieSuffisante): static
    {
        $this->autonomieSuffisante = $autonomieSuffisante;

        return $this;
    }

    public function isConditionsEligibilites(): ?bool
    {
        return $this->conditionsEligibilites;
    }

    public function setConditionsEligibilites(?bool $conditionsEligibilites): static
    {
        $this->conditionsEligibilites = $conditionsEligibilites;

        return $this;
    }

    public function isConditionsTechMatAdm(): ?bool
    {
        return $this->conditionsTechMatAdm;
    }

    public function setConditionsTechMatAdm(bool|null $conditionsTechMatAdm): static
    {
        $this->conditionsTechMatAdm = $conditionsTechMatAdm;

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

    public function getACompterDu(): ?\DateTimeInterface
    {
        return $this->aCompterDu;
    }

    public function setACompterDu(\DateTimeInterface $aCompterDu): static
    {
        $this->aCompterDu = $aCompterDu;

        return $this;
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

    public function getLieuTeletravail(): ?string
    {
        return $this->lieuTeletravail;
    }

    public function setLieuTeletravail(string $lieuTeletravail): static
    {
        $this->lieuTeletravail = $lieuTeletravail;

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

    public function getFonctionExercee(): ?string
    {
        return $this->fonctionExercee;
    }

    public function setFonctionExercee(string $fonctionExercee): static
    {
        $this->fonctionExercee = $fonctionExercee;

        return $this;
    }

    public function getReceptionDemande(): ?\DateTimeInterface
    {
        return $this->receptionDemande;
    }

    public function setReceptionDemande(?\DateTimeInterface $receptionDemande): static
    {
        $this->receptionDemande = $receptionDemande;

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

    public function getQuotitePersonnel(): ?string
    {
        return $this->quotitePersonnel;
    }

    public function setQuotitePersonnel(?string $quotitePersonnel): static
    {
        $this->quotitePersonnel = $quotitePersonnel;

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


    public function isAttestationHonneur(): ?bool
    {
        return $this->attestationHonneur;
    }

    public function setAttestationHonneur(bool $attestationHonneur): static
    {
        $this->attestationHonneur = $attestationHonneur;

        return $this;
    }
}
