<?php

namespace App\Repository;

use App\Entity\HotTopic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HotTopic|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotTopic|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotTopic[]    findAll()
 * @method HotTopic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotTopicRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HotTopic::class);
    }

    // /**
    //  * @return HotTopic[] Returns an array of HotTopic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HotTopic
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
