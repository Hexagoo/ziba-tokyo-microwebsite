<?php

namespace App\Repository;

use App\Entity\ActivityHasMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActivityHasMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityHasMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityHasMedia[]    findAll()
 * @method ActivityHasMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityHasMediaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActivityHasMedia::class);
    }

    // /**
    //  * @return ActivityHasMedia[] Returns an array of ActivityHasMedia objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivityHasMedia
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
