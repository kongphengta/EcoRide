<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
    private ?string $firstname = null;

    #[ORM\Column(length: 128)]
    private ?string $lastname = null;

    #[ORM\Column(length: 50)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $date_inscription = null;

    /**
     * @var Collection<int, Covoiturage>
     */
    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'chauffeur')]
    private Collection $covoiturages;

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
    #[ORM\ManyToMany(targetEntity: Avis::class, inversedBy: 'users')]
    private Collection $avisDonnes;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\ManyToMany(targetEntity: Avis::class, mappedBy: 'receveurs')]
    private Collection $avisRecus;

    public function __construct()
    {
        $this->covoiturages = new ArrayCollection();
        $this->ecoRideRoles = new ArrayCollection();
        $this->avisDonnes = new ArrayCollection();
        $this->avisRecus = new ArrayCollection();
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

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

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
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): static
    {
        $this->date_inscription = $date_inscription;

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
        }

        return $this;
    }

    public function removeAvisDonne(Avis $avisDonne): static
    {
        $this->avisDonnes->removeElement($avisDonne);

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avisRecus;
    }

    public function addAvisRecu(Avis $avisRecu): static
    {
        if (!$this->avisRecus->contains($avisRecu)) {
            $this->avisRecus->add($avisRecu);
            $avisRecu->addReceveur($this);
        }

        return $this;
    }

    public function removeAvisRecu(Avis $avisRecu): static
    {
        if ($this->avisRecus->removeElement($avisRecu)) {
            $avisRecu->removeReceveur($this);
        }

        return $this;
    }
}
