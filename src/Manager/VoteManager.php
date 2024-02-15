<?php

/**
 * Vote Manager
 */

namespace App\Manager;

use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use App\Entity\Voting\VoteResult;
use App\Manager\ConfigurationManager;
use App\Repository\Configuration\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                protected ConfigurationManager $_configurationManager
                                )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function createNewVote(Request $request, Vote $vote, UserInterface $user): Vote
    {
        $candidatesVoted = $request->request->all()['candidat'];
        $candidatesVoted = array_map(fn($id): int => (int)$id, $candidatesVoted);
        foreach ($candidatesVoted as $id) {
            $candidate = $this->entityManager->getRepository(Candidat::class)->find($id);
            $this->createVoteResult($vote, $candidate, $user, true);
        }

        $allCandidate = $this->entityManager->getRepository(Candidat::class)->findAll();
        foreach ($allCandidate as $candidate) {
            if (!in_array($candidate->getId(), $candidatesVoted)) {
                $this->createVoteResult($vote, $candidate, $user, false);
            }
        }

        $configuration = $this->_configurationManager->getConfiguration() ;
        $isWhite = count($candidatesVoted) > 0 ? true : false ;
        $isDead  = count($candidatesVoted) > ($configuration->getNumberWomen() + $configuration->getNumberMen()) ? true : false;
        
        $vote->setIsDead($isDead) ;
        $vote->setIsWhite($isWhite) ;
        $vote->setUser($user);
        $this->entityManager->persist($vote);
        $this->entityManager->flush();

        return $vote;
    }

    private function createVoteResult(Vote $vote, Candidat $candidate, $user, $isVotedOn)
    {
        $voteResult = new VoteResult();
        $voteResult->setIsVotedOn($isVotedOn)
            ->setVote($vote)
            ->setCandidat($candidate)
            ->setResponsible($user);
        $this->entityManager->persist($voteResult);

        return $voteResult;
    }

    public function updateVoteResult(Vote $vote, Request $request)
    {
        $candidates = $request->request->all()['candidat'];
    }
}
