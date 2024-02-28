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
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class VoteManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                protected ConfigurationManager $_configurationManager,
                                protected VoteResultRepository $_voteResultRepository,
                                private readonly Environment $_twig,
                                private readonly ParameterBagInterface $_parameter,
                                private readonly CacheManager $_liipImagineCache
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

    /**
     * Get result list voting
     *
     * @param array $params
     * @return void
     */
    public function getVotingListResult($_params = []){

        $results = [];
        $datas = $this->_voteResultRepository->fetchData($_params);
        foreach($datas as $data){
            $photo = $data['photo'] ;
            if(!empty($photo)){
                $data['photo'] = $this->_liipImagineCache->generateUrl('upload/profil/'.$photo, 'thumbnail_small_50') ;
            }
            
            $results[] = $data ;
        }
        
        return $results;

    }

    /**
     * Generate result voting PDF file
     *
     * @param array $datas
     * @param [type] $type
     * @return void
     */
    public function generateVoteResult($datas = [], $type)
    {

        
        $templating = $this->_twig;
        $logo       = $this->_parameter->get("public_dir") . "/images/logo/logo.png";
        
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html = $templating->render('pdf/voting_result.html.twig', array(
            'datas' => $datas,
            'title' => "Voka-pifidianana ho an'ny ".($type == 'women' ? 'vehivavy' : 'lehilahy'),
            //'logo'  => file_get_contents($logo) ,
            'page' => '1/1'
        ));
        $html2pdf->writeHTML($html);

        $fileName = 'Resultat-'.$type . '-' . time() . '.pdf';
        //$fileName = $_orders->getNum().'.pdf' ;
        $file = $this->_parameter->get("pdf_upload_dir") . "/" . $fileName;
        $html2pdf->Output($file, 'F');


        return $fileName;

    }

    /**
     * Generate item voting PDF file
     *
     * @param array $datas
     * @param [type] $type
     * @return void
     */
    public function generateVoteItem(Vote $vote)
    {

        $voteResults = $this->getVoteResult($vote);
        $type        = $vote->getExecutingVote() ;
        $num         = $vote->getNum();
        $civility    = $vote->getExecutingVote() == Content::VOTE_IN_PROCESS_WOMEN ? 'Mme' : 'Mr';
        $params      = ['civility' => $civility];
        $candidates  = $this->entityManager->getRepository(Candidat::class)->findBy($params);

        $templating = $this->_twig;
        $logo       = $this->_parameter->get("public_dir") . "/images/logo/logo.png";
        
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html = $templating->render('pdf/voting_item.html.twig', array(
            'voteResults' => $voteResults,
            'candidats'  => $candidates,
            'title' => "Latsa-bato ".($type == Content::VOTE_IN_PROCESS_WOMEN ? 'vehivavy' : 'lehilahy')." laharana faha ".$num,
            //'logo'  => file_get_contents($logo) ,
            'page' => '1/1'
        ));
        $html2pdf->writeHTML($html);

        $fileName = 'Voting-'.$type .'-'. $num .'-'. time() . '.pdf';
        //$fileName = $_orders->getNum().'.pdf' ;
        $file = $this->_parameter->get("pdf_upload_dir") . "/" . $fileName;
        $html2pdf->Output($file, 'F');


        return $fileName;

    }

}
