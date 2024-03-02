<?php

namespace App\Controller\Admin\Voting;

use App\Constants\Content;
use App\Constants\UserConstants;
use App\DataTable\VoteDataTableType;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use App\Entity\Voting\VoteResult;
use App\Form\Voting\VoteType;
use App\Manager\CandidatManager;
use App\Manager\ConfigurationManager;
use App\Manager\VoteManager;
use App\Repository\Voting\VoteRepository;
use App\Repository\Voting\VoteResultRepository;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
                                private readonly Security $security,
                                protected ConfigurationManager $configurationManager,
                                protected VoteResultRepository $voteResultRepository,
                                protected CandidatManager $candidatManager)
    {
        
    }

    #[Route('/', name: '.index')]
    public function index(Request $_request, DataTableService $_dataTableService): Response
    {
        $user  = $this->getUser() ;
        $roles = $user->getRoles() ;
        
        if (in_array(UserConstants::USER_ROLE_OPERATOR, $roles)) {
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
        
        $configuration = $this->configurationManager->getConfiguration() ;
        $civility      = $configuration->getExecutingVote() == Content::VOTE_IN_PROCESS_WOMEN ? 'Mme' : 'Mr';
        $voteType      = $configuration->getExecutingVote() == Content::VOTE_IN_PROCESS_WOMEN ? 'femmes' : 'hommes';
        $params        = ['civility' => $civility];
        $candidates = $entityManager->getRepository(Candidat::class)->findBy($params, ['number' => 'ASC']);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $vote->setStatus(Content::VOTE_STATUS_NOT_VERIFY) ;
            $vote->setExecutingVote($configuration->getExecutingVote()) ;
            
            $this->voteManager->createNewVote($request, $vote, $this->getUser());

            //Update voting controll
            $this->voteManager->updateVotingNull($vote, $request) ;
            
            return $this->redirectToRoute('.voting.vote.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Voting/Vote/action.html.twig', [
            'vote'        => $vote,
            'voteType'    => $voteType,
            'form'        => $form,
            'voteResults' => [],
            'voteStatus' => [],
            'candidats'   => $candidates
        ]);
    }


    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vote $vote, EntityManagerInterface $entityManager): Response
    {
        $form   = $this->createForm(VoteType::class, $vote);
        
        $form->handleRequest($request);

        $civility      = $vote->getExecutingVote() == Content::VOTE_IN_PROCESS_WOMEN ? 'Mme' : 'Mr';
        $params        = ['civility' => $civility];
        $candidates    = $entityManager->getRepository(Candidat::class)->findBy($params);
        $voteType      = $vote->getExecutingVote() == Content::VOTE_IN_PROCESS_WOMEN ? 'femmes' : 'hommes';
        
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
            'voteType'     => $voteType,
            'form'          => $form,
            'voteResults'   => $voteResults,
            'candidats'     => $candidates,
            'voteStatus' => Content::VOTE_STATUS_LIST,
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
            $configuration = $this->configurationManager->getConfiguration() ;
            $vote = $entityManager->getRepository(Vote::class)->findOneBy(['num' => $number, 'executingVote' => $configuration->getExecutingVote()]);
            
            if(!empty($vote)){
                $isNew    = false ;
                $status   = $vote->getStatus();
                if($status == Content::VOTE_STATUS_VERIFY_NOT_VALID){
                    $redirect = $this->generateUrl('app.admin.voting.vote.edit', array('id' => $vote->getId())); 
                }
            }
        }
        
        return new JsonResponse(['isNew' => $isNew, 'redirection' => $redirect]) ;
    }

    #[Route('/update', name: '.update.ajax', defaults: [])]
    public function voteUpdate(Request $_request, EntityManagerInterface $entityManager): Response
    {   
        $number   = $_request->get('number', '') ;
        $id       = $_request->get('id', '') ;
        $status   = $_request->get('status', '') ;
        
        if(!empty($id)){

            $vote = $entityManager->getRepository(Vote::class)->find($id);
            if(!empty($vote)){
                $vote->setStatus($status) ;
                $entityManager->persist($vote);
                $entityManager->flush();
            }
        }
        
        return new JsonResponse() ;
    }
    
    #[Route('/export-result', name: '.export.result', defaults: [])]
    public function exportResultPdf(Request $_request): Response
    {   
        $type    = $_request->get('type', '') ;
        $nopoint = $_request->get('nopoint', false) ;
        
        if(!empty($type)){

            $configuration = $this->configurationManager->getConfiguration() ;
            $reserveCount  = $configuration->getNumberReserve() ;

            $results       = [];

            $civility   = $type == 'women' ? 'Mme' : 'Mr' ;
            $maxResult  = $type == 'women' ? $configuration->getNumberWomen() : $configuration->getNumberMen() ;
            
            $limit      = $maxResult + $reserveCount ;
            $params     = ['civility' => $civility, 'limit' => $limit] ;
            if(!empty($nopoint)){
                $params['isWithNullPoint'] = true ;
                unset($params['limit']);
            }

            $results    = $this->voteManager->getVotingListResult($params);
            
            $fileName = $this->voteManager->generateVoteResult($results, $type, $nopoint) ;
            $file     = $this->getParameter("pdf_upload_dir") . "/" . $fileName;
           
            $response = new Response();
            $response->headers->set('Content-type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $fileName ));
            $response->setContent(file_get_contents($file));
            $response->setStatusCode(200);
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            
            return $response;
        }
        
        return new JsonResponse() ;
    }

    #[Route('/export-item/{id}', name: '.export.item', defaults: [])]
    public function exportItemPdf(Vote $vote, Request $_request): Response
    {   
        
        $fileName = $this->voteManager->generateVoteItem($vote) ;
        $file     = $this->getParameter("pdf_upload_dir") . "/" . $fileName;
        
        $response = new Response();
        $response->headers->set('Content-type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$fileName ));
        $response->setContent(file_get_contents($file));
        $response->setStatusCode(200);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
        
    }
}
