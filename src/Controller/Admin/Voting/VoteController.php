<?php

namespace App\Controller\Admin\Voting;

use App\DataTable\VoteDataTableType;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use App\Entity\Voting\VoteResult;
use App\Form\Voting\VoteType;
use App\Repository\Voting\VoteRepository;
use App\Services\Common\DataTableService;
use App\Services\Common\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function __construct( )
    {
        
    }

    #[Route('/', name: '.index')]
    public function index(Request $_request, DataTableService $_dataTableService): Response
    {
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

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vote = new Vote();
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);
        $candidates = $entityManager->getRepository(Candidat::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {

            $all = $request->request->all();
            foreach ($all as $element => $value) {
                if (is_array($element)) {
                    continue;
                }
                if (str_starts_with($element, 'candidat')) {
                    $id = (int)(str_replace('candidat', '', $element));
                    $candidate = $entityManager->getRepository(Candidat::class)->find($id);
                    $isVotedOn = $value === 'on';
                    $voteResult = new VoteResult();
                    $voteResult->setIsVotedOn($isVotedOn)
                        ->setVote($vote)
                        ->setCandidat($candidate)
                        ->setResponsible($this->getUser());
                    $entityManager->persist($voteResult);
                }
            }
            $vote->setUser($this->getUser());
            $entityManager->persist($vote);
            $entityManager->flush();

            return $this->redirectToRoute('.voting.vote.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Voting/Vote/action.html.twig', [
            'vote' => $vote,
            'form' => $form,
            'candidats' => $candidates
        ]);
    }


    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vote $vote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('.voting.vote.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Voting/Vote/action.html.twig', [
            'vote' => $vote,
            'form' => $form,
        ]);
    }

}
