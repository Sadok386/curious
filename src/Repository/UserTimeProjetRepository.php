<?php

namespace App\Repository;

use App\Entity\UserTimeProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserTimeProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTimeProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTimeProjet[]    findAll()
 * @method UserTimeProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTimeProjetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserTimeProjet::class);
    }

//    /**
//     * @return UserTimeProjet[] Returns an array of UserTimeProjet objects
//     */
    public function findByExampleField()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.idUser', 'user')
            ->leftJoin('u.idProjet', 'projet')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?UserTimeProjet
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
