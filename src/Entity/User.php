<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')] // Pour éviter les conflits avec le mot clé "user" de MySQL
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé.')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre adresse e-mail')]
    #[Assert\Email(message: 'Veuillez entrer une adresse e-mail valide')]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre prénom')]
    #[Assert\Length(min: 2, max: 128, minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères', maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères')]
    private ?string $firstname = null;

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre nom')]
    #[Assert\Length(min: 2, max: 128, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères', maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
    private ?string $lastname = null;

    #[ORM\Column(length: 50, nullable: true)]
    // #[Assert\NotBlank(message: "Le téléphone est requis.")]
    #[Assert\Regex(pattern: "/^[0-9\+\-\s\(\)]*$/", message: "Le format du téléphone est invalide.")]
    private ?string $telephone = null; // Type hint ajusté à ?string

    #[ORM\Column(type: 'string', length: 255, nullable: true)] // Pour la fonctionnalité "mot de passe oublié"
    private ?string $resetToken = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)] // Pour la fonctionnalité "mot de passe oublié"
    private ?\DateTimeImmutable $resetTokenCreatedAt = null;

    // Champ rempli dans la 2ème étape (Profil), donc nullable
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null; // Type hint ajusté à ?string

    // Champ rempli dans la 2ème étape (Profil), donc nullable
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date de naissance doit être une date valide.")]
    #[Assert\LessThan("today", message: "La date de naissance ne peut pas être dans le futur.")]
    private ?\DateTimeInterface $dateNaissance = null;

    // le nom du fichier de la photo de profil, nullable
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    // Le sexe de l'utilisateur, pour l'avatar par défaut
    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Choice(choices: ['Homme', 'Femme', 'Autre'], message: 'Veuillez sélectionner un genre valide.')]
    private ?string $sexe = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Le pseudo ne peut pas être vide.")]
    private ?string $pseudo = null;

    // Initialisé dans le constructeur
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    // --- Champs pour la vérification d'email ---
    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $verificationToken = null;

    // --- Champ pour l'état de complétion du profil --- 
    #[ORM\Column(type: 'boolean')]
    private bool $isProfileComplete = false; // Initialisé à false par défaut

    #[ORM\Column(type: 'boolean')]
    private bool $isChauffeur = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    /**
     * @var Collection<int, Covoiturage>
     */
    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'chauffeur')]
    private Collection $covoiturages;

    /** 
     * @var Collection<int, Voiture>
     */
    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: 'proprietaire', cascade: ['persist', 'remove'])]
    private Collection $voitures;


    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Configuration $configuration = null;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    private Collection $ecoRideRoles;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'auteur', orphanRemoval: true)]
    private Collection $avisDonnes;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'receveur', orphanRemoval: true)]
    private Collection $avisRecus;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'passager')]
    private Collection $reservations;

    #[ORM\Column]
    private ?int $credits = 0;

    public function __construct()
    {
        $this->covoiturages = new ArrayCollection();
        $this->voitures = new ArrayCollection();
        $this->ecoRideRoles = new ArrayCollection();
        $this->avisDonnes = new ArrayCollection();
        $this->avisRecus = new ArrayCollection();
        // Initialiser la date d'inscription lors de la création de l'objet
        $this->dateInscription = new \DateTime();
        $this->reservations = new ArrayCollection();
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        // Récupérer les rôles depuis la collection d'entités Role
        $roles = $this->ecoRideRoles->map(function ($role) {
            return $role->getLibelle();
        })->toArray();

        // Garantir que chaque utilisateur a au moins le rôle ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    // La méthode setRoles n'est plus nécessaire, car les rôles sont gérés
    // via les méthodes addEcoRideRole() and removeEcoRideRole().
    // On la supprime pour éviter toute confusion.

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenCreatedAt(): ?\DateTimeImmutable
    {
        return $this->resetTokenCreatedAt;
    }

    public function setResetTokenCreatedAt(?\DateTimeImmutable $resetTokenCreatedAt): self
    {
        $this->resetTokenCreatedAt = $resetTokenCreatedAt;
        return $this;
    }
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;

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

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    // --- Getter/Setter pour is_profile_complete 

    public function isProfileComplete(): bool
    {
        return $this->isProfileComplete;
    }

    public function setIsProfileComplete(bool $isProfileComplete): self
    {
        $this->isProfileComplete = $isProfileComplete;
        return $this;
    }

    public function isChauffeur(): bool
    {
        return $this->isChauffeur;
    }

    public function setIsChauffeur(bool $isChauffeur): self
    {
        $this->isChauffeur = $isChauffeur;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;
        return $this;
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): static
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
            $covoiturage->setChauffeur($this);
        }

        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): static
    {
        if ($this->covoiturages->removeElement($covoiturage)) {
            // set the owning side to null (unless already changed)
            if ($covoiturage->getChauffeur() === $this) {
                $covoiturage->setChauffeur(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Voiture>
     */
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }
    public function addVoiture(Voiture $voiture): static
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
            $voiture->setProprietaire($this);
        }

        return $this;
    }
    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            // set the owning side to null (unless already changed)
            if ($voiture->getProprietaire() === $this) {
                $voiture->setProprietaire(null);
            }
        }

        return $this;
    }

    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(Configuration $configuration): static
    {
        // set the owning side of the relation if necessary
        if ($configuration->getUser() !== $this) {
            $configuration->setUser($this);
        }

        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getEcoRideRoles(): Collection
    {
        return $this->ecoRideRoles;
    }

    public function addEcoRideRole(Role $ecoRideRole): static
    {
        if (!$this->ecoRideRoles->contains($ecoRideRole)) {
            $this->ecoRideRoles->add($ecoRideRole);
        }

        return $this;
    }

    public function removeEcoRideRole(Role $ecoRideRole): static
    {
        $this->ecoRideRoles->removeElement($ecoRideRole);

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisDonnes(): Collection
    {
        return $this->avisDonnes;
    }

    public function addAvisDonne(Avis $avisDonne): static
    {
        if (!$this->avisDonnes->contains($avisDonne)) {
            $this->avisDonnes->add($avisDonne);
            $avisDonne->setAuteur($this);
        }

        return $this;
    }

    public function removeAvisDonne(Avis $avisDonne): static
    {
        if ($this->avisDonnes->removeElement($avisDonne)) {
            // set the owning side to null (unless already changed)
            if ($avisDonne->getAuteur() === $this) {
                $avisDonne->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisRecus(): Collection
    {
        return $this->avisRecus;
    }

    public function addAvisRecu(Avis $avisRecu): static
    {
        if (!$this->avisRecus->contains($avisRecu)) {
            $this->avisRecus->add($avisRecu);
            $avisRecu->setReceveur($this);
        }

        return $this;
    }

    public function removeAvisRecu(Avis $avisRecu): static
    {
        if ($this->avisRecus->removeElement($avisRecu)) {
            // set the owning side to null (unless already changed)
            if ($avisRecu->getReceveur() === $this) {
                $avisRecu->setReceveur(null);
            }
        }

        return $this;
    }
    /**
     * Calcule la note moyenne des avis reçus par l'utilisateur.
     *
     * @return float La note moyenne, ou 0.0 s'il n'y a pas d'avis.
     */
    public function getAverageRating(): float
    {
        $avisRecus = $this->getAvisRecus();

        if ($avisRecus->isEmpty()) {
            return 0.0;
        }

        $totalNotes = 0;
        foreach ($avisRecus as $avis) {
            $totalNotes += $avis->getNote();
        }

        return round($totalNotes / $avisRecus->count(), 1);
    }

    /**
     * Calcule la durée d'adhésion de l'utilisateur et la retourne sous forme de chaîne lisible.
     *
     * @return string La durée d'adhésion (ex: "depuis 2 ans", "depuis 3 mois", "aujourd'hui").
     */
    public function getMembershipDurationAsString(): string
    {
        if (!$this->dateInscription) {
            return 'N/A';
        }

        $now = new \DateTime();
        $interval = $this->dateInscription->diff($now);

        if ($interval->y >= 1) {
            $plural = $interval->y > 1 ? 's' : '';
            return "depuis {$interval->y} an{$plural}";
        }
        if ($interval->m >= 1) {
            return "depuis {$interval->m} mois";
        }
        if ($interval->d >= 1) {
            $plural = $interval->d > 1 ? 's' : '';
            return "depuis {$interval->d} jour{$plural}";
        }

        return "aujourd'hui";
    }

    /**
     * Retourne l'URL de l'avatar de l'utilisateur.
     * Gère la photo de profil, l'avatar par défaut selon le sexe, ou un avatar généré.
     *
     * @param int $size La taille de l'avatar pour ui-avatars.com.
     * @return string L'URL complète ou le chemin relatif de l'avatar.
     */
    public function getAvatarUrl(int $size = 150): string
    {
        if ($this->photo) {
            return 'uploads/' . $this->photo;
        }

        if ($this->sexe === 'Homme') {
            return 'images/avatar_homme.png';
        }

        if ($this->sexe === 'Femme') {
            return 'images/avatar_femme.png';
        }

        // Fallback sur ui-avatars.com
        $name = urlencode($this->firstname . '+' . $this->lastname);
        return "https://ui-avatars.com/api/?name={$name}&background=random&size={$size}";
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setPassager($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getPassager() === $this) {
                $reservation->setPassager(null);
            }
        }

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;

        return $this;
    }
}
