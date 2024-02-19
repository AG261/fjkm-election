<?php

namespace App\Controller;

use App\Manager\CandidatManager;
use App\Manager\ConfigurationManager;
use App\Manager\VoteManager;
use App\Repository\Voting\VoteResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    #[Route('/', name: 'app_result')]
    public function index(CandidatManager $candidatManager): Response
    {   
        $candidats  = $candidatManager->getCandidatCount();
        
        return $this->render('Result/index.html.twig', [
            'controller_name' => 'ResultController',
            'candidats'       => $candidats
        ]);
    }

    public function resultAjax(VoteResultRepository $repository, VoteManager $voteManager, ConfigurationManager $configurationManager): Response
    {
        $votingCount = $voteManager->getVotingCount();
        $configuration = $configurationManager->getConfiguration() ;
        
        $datas   = [] ;
        $mens    = $repository->fetchData(['civility' => 'Mr', 'limit' => $configuration->getNumberMen()]);
        $womenMmes  = $repository->fetchData(['civility' => 'Mme', 'limit' => $configuration->getNumberWomen()]);
        $datas = array_merge($datas, $mens) ;
        $datas = array_merge($datas, $womenMmes) ;

        //$data  = $repository->fetchData();
        $results = [
                        'count' => $votingCount,
                        'data'  => $datas,
                        
                  ] ;
        
        return new JsonResponse(json_encode($results));
    }
}
