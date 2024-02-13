<?php

namespace App\Repository\Voting;

use App\Entity\Voting\VoteResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VoteResult>
 *
 * @method VoteResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method VoteResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method VoteResult[]    findAll()
 * @method VoteResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoteResult::class);
    }

//    /**
//     * @return VoteResult[] Returns an array of VoteResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VoteResult
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
