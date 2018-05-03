<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTimeProjetRepository")
 */
class UserTimeProjet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projetsTime")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"nom" = "ASC"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Projet", inversedBy="usersTime", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"nom" = "ASC"})
     */
    private $projet;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $time;

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setTime(?float $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function incrementTime()
    {
        $this->time += 0.5;
    }
}
