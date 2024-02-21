<?php

/**
 * Vote Manager
 */

namespace App\Manager;

use App\Constants\Content;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use App\Entity\Voting\VoteResult;
use App\Manager\ConfigurationManager;
use App\Repository\Configuration\ConfigurationRepository;
use App\Repository\Voting\VoteResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                protected ConfigurationManager $_configurationManager,
                                protected VoteResultRepository $_voteResultRepository
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
        $candidatesVoted = isset($request->request->all()['candidat']) ? $request->request->all()['candidat'] : [];
        $candidatesVoted = count($candidatesVoted) > 0 ? array_map(fn($id): int => (int)$id, $candidatesVoted) : [];
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

        $vote->setUser($user);
        $this->entityManager->persist($vote);
        $this->entityManager->flush();

        return $vote;
    }

    private function createVoteResult(Vote $vote, Candidat $candidate, $user, $isVotedOn): VoteResult
    {
        
        $voteResult = new VoteResult();
        $voteResult->setIsVotedOn($isVotedOn)
            ->setVote($vote)
            ->setCandidat($candidate)
            ->setResponsible($user);
        $this->entityManager->persist($voteResult);

        return $voteResult;
    }

    public function updateVoteResult(Vote $vote, Request $request): void
    {
        $candidatesVoted = isset($request->request->all()['candidat']) ? $request->request->all()['candidat'] : [];
        $candidatesVoted = count($candidatesVoted) > 0 ? array_map(fn($id): int => (int)$id, $candidatesVoted) : [];
        $allCandidate = $this->entityManager->getRepository(Candidat::class)->findAll();
        foreach ($allCandidate as $candidate) {
            $oldCandidateVoteResult = $this->entityManager->getRepository(VoteResult::class)->findOneBy(['vote' => $vote, 'candidat' => $candidate]);
            if (in_array($candidate->getId(), $candidatesVoted)) {
                $oldCandidateVoteResult->setIsVotedOn(true);
            } else {
                $oldCandidateVoteResult->setIsVotedOn(false);
            }
        }
    }

    /**
     * Update voting null with controll     *
     * @param Vote $vote
     * @param Request $request
     * @return void
     */
    public function updateVotingNull(Vote $vote, Request $request){

        $candidatesVoted = isset($request->request->all()['candidat']) ? $request->request->all()['candidat'] : [];
        $candidatesVoted = count($candidatesVoted) > 0 ? array_map(fn($id): int => (int)$id, $candidatesVoted) : [];


        $configuration = $this->_configurationManager->getConfiguration() ;
        $voteMax       = $configuration->getExecutingVote() == Content::VOTE_IN_PROCESS_WOMEN ? $configuration->getNumberWomen() : $configuration->getNumberMen();
        
        $isWhite = count($candidatesVoted) == 0 ? true : false ;
        $isDead  = count($candidatesVoted) > $voteMax ? true : false;
        
        $vote->setIsDead($isDead) ;
        $vote->setIsWhite($isWhite) ;

        $this->entityManager->persist($vote);
        $this->entityManager->flush();
        
        return $vote;
    }

    /**
     * Get result voting
     *
     * @param Vote $vote
     * @return mixed
     */
    public function getVoteResult(Vote $vote){

        $results = [];
        $voteResults = $this->entityManager->getRepository(VoteResult::class)->findBy(['vote' => $vote]);

        foreach($voteResults as $voteResult){
            $results[$voteResult->getCandidat()->getId()] = $voteResult->isIsVotedOn() ;
        }

        return $results;

    }

    /**
     * Get result voting
     *
     * @param array $params
     * @return void
     */
    public function getVotingCount($params = []){

        $results = [];
        $votes = $this->entityManager->getRepository(Vote::class)->findBy($params);

        $total   = count($votes) ;
        $isDead  = 0 ;
        $isWhite = 0 ;
        $isGood  = 0 ;
        foreach($votes as $vote){
            if(!empty($vote->isIsDead())){
                $isDead++;
            }

            if(!empty($vote->isIsWhite())){
                $isWhite++;
            }
        }

        $isGood  = $total - $isDead - $isWhite ;

        $results = ['total' => $total, 'isGood' => $isGood, 'isDead' => $isDead, 'isWhite' => $isWhite] ;
        
        return $results;

    }

}
