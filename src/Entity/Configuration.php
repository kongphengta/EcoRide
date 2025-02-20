<?php

namespace App\Entity;

use App\Repository\ConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'configuration')]
    private Collection $users;

    /**
     * @var Collection<int, Parametre>
     */
    #[ORM\ManyToMany(targetEntity: Parametre::class, inversedBy: 'configurations')]
    private Collection $parametre;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->parametre = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $user->addConfiguration($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeConfiguration($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Parametre>
     */
    public function getParametre(): Collection
    {
        return $this->parametre;
    }

    public function addParametre(Parametre $parametre): static
    {
        if (!$this->parametre->contains($parametre)) {
            $this->parametre->add($parametre);
        }

        return $this;
    }

    public function removeParametre(Parametre $parametre): static
    {
        $this->parametre->removeElement($parametre);

        return $this;
    }
}
