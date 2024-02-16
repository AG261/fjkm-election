<?php

namespace App\Controller\Admin\Dashboard;

use App\Common\Constants\UserConstants;
use App\Manager\CandidatManager;
use App\Manager\VoteManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * Construct
     *
     */
    public function __construct( protected CandidatManager $_candidatManager,
                                 protected VoteManager $_voteManager)
    {
        
    }
    #[Route('/dashboard', name:'.dashboard')]
    public function index(Request $_request): Response
    {

        $user  = $this->getUser() ;
        $roles = $user->getRoles() ;
        
        if (in_array(UserConstants::USER_ROLE_OPERATOR, $roles) 
            && (!in_array(UserConstants::USER_ROLE_ADMIN, $roles)
            || !in_array(UserConstants::USER_ROLE_VALIDATOR, $roles))) {
            $route = 'app_result' ;
            return $this->redirectToRoute($route);
        }
        
        $canditats  = $this->_candidatManager->getCandidatCount();
        $votingCount = $this->_voteManager->getVotingCount();
        return $this->render('Admin/Dashboard/index.html.twig', [
            'candidats'    => $canditats,
            'votingCount' => $votingCount
        ]);
    }

}