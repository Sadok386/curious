<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Projet", inversedBy="enfants")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Projet", mappedBy="parent")
     */
    private $enfants;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
     *     maxSize = "4096k",
     *     mimeTypes = {"image/png", "image/jpeg", "image/jpg", "image/gif"},
     * )
     *
     */
    private $image;

    public function __construct()
    {
        $this->usersTime = new ArrayCollection();
        $this->enfants = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getEnfants()
    {
        return $this->enfants;
    }

    /**
     * @param mixed $enfants
     */
    public function setEnfants($enfants)
    {
        $this->enfants = $enfants;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
