<?php

namespace App\Controller\Admin\Voting;

use App\Common\Constants\UserConstants;
use App\Constants\Content;
use App\DataTable\VoteDataTableType;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use App\Entity\Voting\VoteResult;
use App\Form\Voting\VoteType;
use App\Manager\VoteManager;
use App\Repository\Voting\VoteRepository;
use App\Services\Common\DataTableService;
use App\Services\Common\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/voting/vote', name : '.voting.vote')]
class VoteController extends AbstractController
{   
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * Construct
     *
     */
    public function __construct(private readonly VoteManager $voteManager,
                                private readonly Security $security)
    {
        
    }

    #[Route('/', name: '.index')]
    public function index(Request $_request, DataTableService $_dataTableService): Response
    {
        $user  = $this->getUser() ;
        $roles = $user->getRoles() ;
        
        if (in_array(UserConstants::USER_ROLE_OPERATOR, $roles) 
            && (!in_array(UserConstants::USER_ROLE_ADMIN, $roles)
            || !in_array(UserConstants::USER_ROLE_VALIDATOR, $roles))) {
            $route = 'app.admin.voting.vote.new' ;
            return $this->redirectToRoute($route);
        }

        $routeParams = $_request->attributes->get('_route_params');

        $ajaxRequest = $_request->isXmlHttpRequest();
        $options     = [] ;

        if($ajaxRequest){
            $searchs = $_request->get('search', []) ;
            if(isset($searchs['value']) && !empty($searchs['value'])){
                $options['query'] = $searchs['value'];
            }

        }

        $table = $_dataTableService->createDataTable(VoteDataTableType::class, $options);
        $table->handleRequest($_request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        
        return $this->render('Admin/Voting/Vote/index.html.twig', [
            'datatable' => $table,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vote = new Vote();
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);
        $candidates = $entityManager->getRepository(Candidat::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $vote->setStatus(Content::VOTE_STATUS_NOT_VERIFY) ;
            $this->voteManager->createNewVote($request, $vote, $this->getUser());

            //Update voting controll
            $this->voteManager->updateVotingNull($vote, $request) ;
            
            return $this->redirectToRoute('.voting.vote.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Voting/Vote/action.html.twig', [
            'vote'        => $vote,
            'form'        => $form,
            'voteResults' => [],
            'candidats'   => $candidates
        ]);
    }


    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vote $vote, EntityManagerInterface $entityManager): Response
    {
        $form   = $this->createForm(VoteType::class, $vote);
        
        $form->handleRequest($request);
        $candidates = $entityManager->getRepository(Candidat::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->voteManager->updateVoteResult($vote, $request);
            $entityManager->flush();

            //UPdate voting controll
            $this->voteManager->updateVotingNull($vote, $request) ;

            return $this->redirectToRoute('.voting.vote.index', [], Response::HTTP_SEE_OTHER);
        }

        $voteResults = $this->voteManager->getVoteResult($vote);
       
        return $this->render('Admin/Voting/Vote/action.html.twig', [
            'vote'          => $vote,
            'form'          => $form,
            'voteResults'   => $voteResults,
            'candidats'     => $candidates,
        ]);
    }


    #[Route('/search', name: '.search.ajax', defaults: [])]
    public function voteSearch(Request $_request, EntityManagerInterface $entityManager): Response
    {   
        $number   = $_request->get('number', '') ;

        $isNew    = true ;
        $redirect = '' ;
        $status   = Content::VOTE_STATUS_LIST ;
        if(!empty($number)){

            $vote = $entityManager->getRepository(Vote::class)->findOneBy(['num' => $number]);
            if(!empty($vote)){
                $isNew    = false ;
                $status   = $vote->getStatus();
                $redirect = ($status != Content::VOTE_STATUS_VERIFY_NOT_VALID) ? '' : $this->generateUrl('app.admin.voting.vote.edit', array('id' => $vote->getId())); 
                $redirect = $this->generateUrl('app.admin.voting.vote.edit', array('id' => $vote->getId())) ;
            }
        }
        
        return new JsonResponse(['isNew' => $isNew, 'redirection' => $redirect]) ;
    }

}
