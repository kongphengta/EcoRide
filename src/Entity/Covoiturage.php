<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDepart = null;

    #[ORM\Column(length: 50)]
    private ?string $heureDepart = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuDepart = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateArrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $heureArrivee = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuArrivee = null;

    #[ORM\Column(length: 255)]
    private ?string $staut = null;

    #[ORM\Column]
    private ?int $nbPlace = null;

    #[ORM\Column]
    private ?float $prixPersonne = null;

    #[ORM\Column]
    private ?int $nbPlaceRestante = null;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    private ?User $chauffeur = null;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voiture $voiture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepart(): ?\DateTimeImmutable
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeImmutable $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getHeureDepart(): ?string
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(string $heureDepart): static
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): static
    {
        $this->lieuDepart = $lieuDepart;

        return $this;
    }

    public function getDateArrivee(): ?\DateTimeImmutable
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTimeImmutable $dateArrivee): static
    {
        $this->dateArrivee = $dateArrivee;

        return $this;
    }

    public function getHeureArrivee(): ?string
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(string $heureArrivee): static
    {
        $this->heureArrivee = $heureArrivee;

        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieuArrivee;
    }

    public function setLieuArrivee(string $lieuArrivee): static
    {
        $this->lieuArrivee = $lieuArrivee;

        return $this;
    }

    public function getStaut(): ?string
    {
        return $this->staut;
    }

    public function setStaut(string $staut): static
    {
        $this->staut = $staut;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): static
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getPrixPersonne(): ?float
    {
        return $this->prixPersonne;
    }

    public function setPrixPersonne(float $prixPersonne): static
    {
        $this->prixPersonne = $prixPersonne;

        return $this;
    }

    public function getNbPlaceRestante(): ?int
    {
        return $this->nbPlaceRestante;
    }

    public function setNbPlaceRestante(int $nbPlaceRestante): static
    {
        $this->nbPlaceRestante = $nbPlaceRestante;

        return $this;
    }

    public function getChauffeur(): ?User
    {
        return $this->chauffeur;
    }

    public function setChauffeur(?User $chauffeur): static
    {
        $this->chauffeur = $chauffeur;

        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): static
    {
        $this->voiture = $voiture;

        return $this;
    }
}
