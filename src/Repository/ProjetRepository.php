<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Projet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projet[]    findAll()
 * @method Projet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Projet::class);
    }

//    /**
//     * @return Projet[] Returns an array of Projet objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function findAllChildrenOfChildren(){
        return $this->createQueryBuilder('p')
            ->andWhere('p.parent IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findTime($projet){
        return $this->createQueryBuilder('p')
            ->andWhere('p.parent IS NOT NULL')
            ->andWhere('p.id = :val')
            ->setParameter('val', $projet)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllRacine(){
        return $this->findBy(array("parent" => null), array("nom" => "ASC"));
    }

    /*
    public function findOneBySomeField($value): ?Projet
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
