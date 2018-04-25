<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetRepository")
 */
class Projet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserTimeProjet", mappedBy="projet")
     */
    private $usersTime;

    public function __construct()
    {
        $this->usersTime = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function incrementTotal()
    {
        $this->total += 0.5;
    }

    /**
     * @return Collection|UserTimeProjet[]
     */
    public function getUsersTime(): Collection
    {
        return $this->usersTime;
    }

    public function setUsersTime($usersTime)
    {
        $this->usersTime = $usersTime;
    }
}
