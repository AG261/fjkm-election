<?php

/**
 * User Manager
 */

namespace App\Manager;

use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use App\Entity\Voting\VoteResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function createNewVote(Request $request, Vote $vote, UserInterface $user): Vote
    {
        $candidates = $request->request->all()['candidat'];
        foreach ($candidates as $id) {
            $candidate = $this->entityManager->getRepository(Candidat::class)->find((int)$id);
            $voteResult = new VoteResult();
            $voteResult->setIsVotedOn(true)
                ->setVote($vote)
                ->setCandidat($candidate)
                ->setResponsible($user);
            $this->entityManager->persist($voteResult);
        }
        $vote->setUser($user);
        $this->entityManager->persist($vote);
        $this->entityManager->flush();

        return $vote;
    }
}
