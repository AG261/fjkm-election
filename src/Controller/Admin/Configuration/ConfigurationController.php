<?php

namespace App\Controller\Admin\Configuration;

use App\Entity\Configuration\Configuration;
use App\Form\Configuration\ConfigurationType;
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
    public function __construct( protected ConfigurationRepository $_configurationRepository,
                                protected EntityManagerInterface $_entityManager)
    {
        
    }

    
    #[Route('/', name: '.index', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $configurations = $this->_configurationRepository->findAll() ;
        if(count($configurations) > 0){
            $configuration = $configurations[0];
        }else{
            $configuration = new Configuration();
            $configuration->setNumberMen(1) ;
            $configuration->setNumberWomen(1) ;
            $this->_entityManager->persist($configuration) ;
            $this->_entityManager->flush() ;
        }
        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            
            return $this->redirectToRoute('.configuration.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Configuration/action.html.twig', [
            'configuration' => $configuration,
            'form' => $form,
            
        ]);
    }

}
