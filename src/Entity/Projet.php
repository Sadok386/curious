<?php

namespace App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetRepository")
 */
class Projet extends Controller
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
     * @ORM\OneToMany(targetEntity="App\Entity\UserTimeProjet", mappedBy="projet", cascade={"remove"})
     * @ORM\OrderBy({"user" = "ASC"})
     */
    private $usersTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Projet", inversedBy="enfants")
     * @ORM\OrderBy({"nom" = "ASC"})
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Projet", mappedBy="parent", cascade={"remove"})
     * @ORM\OrderBy({"nom" = "ASC"})
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


        $users = $this->getUsersTime();
        $users = array_map(function ($user) {
            return $user->getTime();
        }, $users->toArray());
        $total = array_reduce($users, function ($userPrecTime, $userSuivTime) {
            return $userPrecTime + $userSuivTime;
        }, 0);

        $enfants = $this->getEnfants();
        $enfants = array_map(function ($enfant){
                return $enfant->getTotal();
            },$enfants->toArray());
        $total += array_reduce($enfants, function ($enfantPrecTotal, $enfantSuivTotal){
           return $enfantPrecTotal + $enfantSuivTotal;
        },0);
        return $total;
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

    public function __toString() {
        return $this->getNom();
    }
}
