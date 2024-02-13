<?php

namespace App\Repository;

use App\Entity\Votings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Votings>
 *
 * @method Votings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Votings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Votings[]    findAll()
 * @method Votings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VotingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Votings::class);
    }

//    /**
//     * @return Votings[] Returns an array of Votings objects
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

//    public function findOneBySomeField($value): ?Votings
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
