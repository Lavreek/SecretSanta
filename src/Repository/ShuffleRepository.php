<?php

namespace App\Repository;

use App\Entity\Shuffle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shuffle>
 *
 * @method Shuffle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shuffle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shuffle[]    findAll()
 * @method Shuffle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShuffleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shuffle::class);
    }

    //    /**
    //     * @return Shuffle[] Returns an array of Shuffle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Shuffle
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
