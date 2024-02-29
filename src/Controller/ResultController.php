<?php

namespace App\Controller;

use App\Constants\Content;
use App\Manager\CandidatManager;
use App\Manager\ConfigurationManager;
use App\Manager\VoteManager;
use App\Repository\Voting\VoteResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    #[Route('/', name: 'app_result_full', defaults:['type' => 'all', 'nopoint' => false])]
    #[Route('/resultat/femmes', name: 'app_result_full_women', defaults:['type' => 'women', 'nopoint' => true])]
    #[Route('/resultat/hommes', name: 'app_result_full_men', defaults:['type' => 'men', 'nopoint' => true])]
    public function index(Request $request, VoteResultRepository $repository, CandidatManager $candidatManager, ConfigurationManager $configurationManager): Response
    {   
        $type     = $request->get('type', 'all') ;
        $nopoint  = $request->get('nopoint', false) ;
        $civility = $type == 'women' ? 'mme' : 'mr' ;
        $typeName = $type == 'women' ? 'vehivavy' : 'lehilahy' ;
        
        $candidats  = $candidatManager->getCandidatCount();
        $configuration = $configurationManager->getConfiguration() ;
        $menResults    = $repository->fetchData(['civility' => 'Mr', 'limit' => $configuration->getNumberMen()]);
        $womenResults  = $repository->fetchData(['civility' => 'Mme', 'limit' => $configuration->getNumberWomen()]);

        return $this->render('Result/index.html.twig', [
            'controller_name' => 'ResultController',
            'candidats'       => $candidats,
            'menResults'      => $menResults,
            'womenResults'    => $womenResults,
            'civility'        => $civility,
            'type'            => $type,
            'typeName'        => $typeName,
            'nopoint'         => $nopoint
        ]);
    }

    #[Route('/resultat-simple/femmes', name: 'app_result_simple_type_women', defaults:['type' => 'women'])]
    #[Route('/resultat-simple/hommes', name: 'app_result_simple_type_men', defaults:['type' => 'men'])]
    public function resultType(Request $request, VoteResultRepository $repository, CandidatManager $candidatManager, ConfigurationManager $configurationManager): Response
    {   
        $type = $request->get('type', 'women') ;
        $civility = $type == 'women' ? 'mme' : 'mr' ;
        $typeName = $type == 'women' ? 'vehivavy' : 'lehilahy' ;
        
        $candidats      = $candidatManager->getCandidatCount();
        $configuration  = $configurationManager->getConfiguration() ;

        return $this->render('Result/result-type.html.twig', [
            'controller_name' => 'ResultController',
            'candidats'       => $candidats,
            'civility'        => $civility,
            'type'            => $type,
            'typeName'        => $typeName
        
        ]);
    }

    public function resultAjax(Request $request, VoteResultRepository $repository, VoteManager $voteManager, ConfigurationManager $configurationManager): Response
    {
        $configuration    = $configurationManager->getConfiguration() ;
        $votingMenCount   = $voteManager->getVotingCount(['executingVote' => Content::VOTE_IN_PROCESS_MEN]);
        $votingWomenCount = $voteManager->getVotingCount(['executingVote' => Content::VOTE_IN_PROCESS_WOMEN]);
        $maxResult        = 0 ;
        $reserveCount     = $configuration->getNumberReserve() ;
        $type             = $request->get('type', '') ;
        $nopoint          = $request->get('nopoint', '') ;
        
        if(empty($type) || $type == 'all'){
           
            $datas      = [] ;
            $mens       = $voteManager->getVotingListResult(['civility' => 'Mr', 'limit' => $configuration->getNumberMen()]);
            $womenMmes  = $voteManager->getVotingListResult(['civility' => 'Mme', 'limit' => $configuration->getNumberWomen()]);
            $datas = array_merge($datas, $mens) ;
            $datas = array_merge($datas, $womenMmes) ;
        }else{
            $civility   = $type == 'women' ? 'Mme' : 'Mr' ;
            $maxResult  = $type == 'women' ? $configuration->getNumberWomen() : $configuration->getNumberMen() ;
            
            $limit      = $maxResult + $reserveCount ;
            $params     = ['civility' => $civility, 'limit' => $limit] ;
            
            if(!empty($nopoint)){
                $params['isWithNullPoint'] = true ;
                unset($params['limit']);
                
            }
            
            $datas      = $voteManager->getVotingListResult($params);
            
        }


        //$data  = $repository->fetchData();
        $results = [
                        'count'     => ['men' => $votingMenCount, 'women' => $votingWomenCount],
                        'data'      => $datas,
                        'maxResult' => $maxResult,
                        'reserveCount' => $reserveCount
                  ] ;
        
        return new JsonResponse(json_encode($results));
    }
}
