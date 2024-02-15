<?php

/**
 * Candidat Manager
 */

namespace App\Manager;

use App\Repository\Voting\CandidatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;

class CandidatManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                protected CandidatRepository $_candidatRepository
                                )
    {
    }

    /**
     * Get candidat count
     */
    public function getCandidatCount(){
        
        $candidats = $this->_candidatRepository->findAll();
        $total     = count($candidats) ;

        $men       = 0;
        $women     = 0;
        foreach($candidats as $candidat){
            $civility = $candidat->getCivility();
            if($civility == "Mr"){
                $men++;
            }else{
                $women++;
            }
        }

        return ['total' => $total, 'men' => $men, 'women' => $women];
    }
}
