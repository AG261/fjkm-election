<?php

namespace App\Controller\Admin\Voting;

use App\DataTable\CandidatDataTableType;
use App\DataTable\UserDataTableType;
use App\Entity\Voting\Candidat;
use App\Form\Voting\CandidatType;
use App\Repository\Voting\CandidatRepository;
use App\Services\Common\DataTableService;
use App\Services\Common\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/voting/candidat', name : '.voting.candidat')]
class CandidatController extends AbstractController
{   
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * Construct
     *
     * @param FileUploader $fileUploader
     */
    public function __construct(FileUploader $fileUploader, )
    {
        $this->fileUploader = $fileUploader;
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

        $table = $_dataTableService->createDataTable(CandidatDataTableType::class, $options);
        $table->handleRequest($_request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('Admin/Voting/candidat/index.html.twig', [
            'datatable' => $table,
        ]);
    }

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidat = new Candidat();
        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $profileFile = $form->get('photo')->getData();
            if ($profileFile) {
                $profileFileName = $this->fileUploader->upload($profileFile, $this->getParameter("profil_upload_dir"));
                $candidat->setPhoto($profileFileName);
            }

            $entityManager->persist($candidat);
            $entityManager->flush();

            return $this->redirectToRoute('.voting.candidat.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Voting/candidat/action.html.twig', [
            'candidat' => $candidat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '.show', methods: ['GET'])]
    public function show(Candidat $candidat): Response
    {
        return $this->render('Admin/Voting/candidat/show.html.twig', [
            'candidat' => $candidat,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidat $candidat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileFile = $form->get('photo')->getData();
            if ($profileFile) {
                $profileFileName = $this->fileUploader->upload($profileFile, $this->getParameter("profil_upload_dir"));
                $candidat->setPhoto($profileFileName);
            }

            $entityManager->persist($candidat);
            $entityManager->flush();

            return $this->redirectToRoute('.voting.candidat.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Voting/candidat/action.html.twig', [
            'candidat' => $candidat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '.delete', methods: ['POST'])]
    public function delete(Request $request, Candidat $candidat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidat->getId(), $request->request->get('_token'))) {

            $votes = $candidat->getVoteResults();
            foreach ($votes as $vote) {
                $candidat->removeVoteResult($vote);
            }

            $entityManager->remove($candidat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('.voting.candidat.index', [], Response::HTTP_SEE_OTHER);
    }
}
