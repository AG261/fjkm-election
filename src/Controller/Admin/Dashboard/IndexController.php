<?php

namespace App\Controller\Admin\Dashboard;

use App\Form\DashboardSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/dashboard', name:'.dashboard')]
    public function index(Request $_request): Response
    {
        return$this->render('Admin/Dashboard/index.html.twig', [
            
        ]);
    }

}