<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $conducteur_id = null;

    #[ORM\Column(length: 255)]
    private ?string $ville_depart = null;

    #[ORM\Column(length: 255)]
    private ?string $ville_arrivee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_depart = null;

    #[ORM\Column]
    private ?int $nombre_places = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_arrivee_estimee = null;

    #[ORM\Column]
    private ?int $itineraire_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $voiture_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConducteurId(): ?int
    {
        return $this->conducteur_id;
    }

    public function setConducteurId(int $conducteur_id): static
    {
        $this->conducteur_id = $conducteur_id;

        return $this;
    }

    public function getVilleDepart(): ?string
    {
        return $this->ville_depart;
    }

    public function setVilleDepart(string $ville_depart): static
    {
        $this->ville_depart = $ville_depart;

        return $this;
    }

    public function getVilleArrivee(): ?string
    {
        return $this->ville_arrivee;
    }

    public function setVilleArrivee(string $ville_arrivee): static
    {
        $this->ville_arrivee = $ville_arrivee;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->date_depart;
    }

    public function setDateDepart(\DateTimeInterface $date_depart): static
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getNombrePlaces(): ?int
    {
        return $this->nombre_places;
    }

    public function setNombrePlaces(int $nombre_places): static
    {
        $this->nombre_places = $nombre_places;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

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

    public function getHeureArriveeEstimee(): ?\DateTimeInterface
    {
        return $this->heure_arrivee_estimee;
    }

    public function setHeureArriveeEstimee(\DateTimeInterface $heure_arrivee_estimee): static
    {
        $this->heure_arrivee_estimee = $heure_arrivee_estimee;

        return $this;
    }

    public function getItineraireId(): ?int
    {
        return $this->itineraire_id;
    }

    public function setItineraireId(int $itineraire_id): static
    {
        $this->itineraire_id = $itineraire_id;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeInterface $date_publication): static
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getVoitureId(): ?int
    {
        return $this->voiture_id;
    }

    public function setVoitureId(int $voiture_id): static
    {
        $this->voiture_id = $voiture_id;

        return $this;
    }
}
