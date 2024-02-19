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

    /**
     * @return VoteResult[] Returns an array of VoteResult objects
     */
    public function fetchData($_params = []): array
    {
        $query = $this->createQueryBuilder('v')
                      ->select('DISTINCT c.id, c.civility, c.firstname, c.lastname, c.photo, c.number, c.numberid, SUM(CASE WHEN v.isVotedOn = true THEN 1 ELSE 0 END) AS vote_count')
                      ->join('v.candidat', 'c') ;

        if(isset($_params['civility']) && !empty($_params['civility'])){
            $query->andWhere('c.civility = :civility')
                  ->setParameter('civility', $_params['civility']) ;
        }

        if(isset($_params['limit']) && $_params['limit'] > 0){
            $query->setMaxResults($_params['limit']);
        }
        
        $query->groupBy('c.id')
              ->orderBy('vote_count', 'DESC') ;

        return $query->getQuery()
               ->getResult()
        ;
    }

}
