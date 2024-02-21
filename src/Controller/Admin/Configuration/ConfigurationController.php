<?php

namespace App\Controller\Admin\Configuration;

use App\Entity\Configuration\Configuration;
use App\Form\Configuration\ConfigurationType;
use App\Manager\ConfigurationManager;
use App\Repository\Configuration\ConfigurationRepository;
use App\Services\Common\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use FTP\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/configuration', name : '.configuration')]
class ConfigurationController extends AbstractController
{   

    /**
     * Construct
     *
     */
    public function __construct( protected ConfigurationManager $_configurationManager,
                                protected EntityManagerInterface $_entityManager)
    {
        
    }

    #[Route('/', name: '.index', methods: ['GET', 'POST'], defaults: ['type' => 'configuration'])]
    #[Route('/vote', name: '.vote', methods: ['GET', 'POST'], defaults: ['type' => 'vote'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $routeParams     = $request->attributes->get('_route_params');
        $isConfiguration = (isset($routeParams['type']) && $routeParams['type'] == 'configuration') ? true : false  ;
        $urlRedirect     = (isset($routeParams['type']) && $routeParams['type'] == 'configuration') ? '.configuration.index' : '.configuration.vote'  ;
        $configuration = $this->_configurationManager->getConfiguration() ;
       
        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->flush();
            
            return $this->redirectToRoute($urlRedirect, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Configuration/action.html.twig', [
            'configuration'    => $configuration,
            'form'             => $form,
            'isConfiguration' => $isConfiguration
        ]);
    }


}
